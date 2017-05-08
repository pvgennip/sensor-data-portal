<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use App\Sensor;

class SensorController extends Controller
{
    
    protected $sensorTypes = ['hap_sum'=>'Household Air Pollution (HAP) & Stove Usage Monitoring (SUM)','ssu_wap'=>'Standard Surface Unit (SSU) & Water Pressure unit (WAP)','Other'=>'Other'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sensors = Sensor::orderBy('id','DESC')->paginate(10);
        
        return view('sensors.index',compact('sensors'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function uncoupled(Request $request)
    {
        $client = new \Influx;

        $uncoupled_sensors  = collect();
        $where_not          = join(Sensor::orderBy('key')->pluck('key')->toArray(), "\' AND sensor_id != \'");
        $result             = $client::query('SELECT "sensor_id",* FROM "ssu","hap" WHERE sensor_id != \'\' AND sensor_id != \''.$where_not.'\' GROUP BY "sensor_id" ORDER BY time DESC LIMIT 1');
        $uncoupled_sensors  = collect($result->getPoints());

        //die(print_r($uncoupled_sensors));

        return view('sensors.uncoupled',compact('uncoupled_sensors'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        $sensors = Sensor::orderBy('id','DESC')->paginate(10);
        
        // Add data from influxdb
        $client = new \Influx;
        foreach ($sensors as $id => $sensor) 
        {
            $type = strtolower($sensor->type);
            if ($type == "ssu_wap" || $type == "hap_sum")
            {
                $result = $client::query('SELECT * FROM "ssu","hap","'.$type.'" WHERE "sensor_id" = \''.$sensor->key.'\' AND time > now() - 365d GROUP BY "sensor_id" ORDER BY time DESC LIMIT 1');
                //die(print_r($result));
                if ($result)
                {
                    $sensordata = $result->getPoints();
                    if (count($sensordata) > 0)
                    {
                        //die(print_r($sensordata));
                        $sensors[$id]->date  = $sensordata[0]['time'];
                        if ($type == "ssu_wap")
                        {
                            // Depth = Depth(m)=(WAP pressure- BME280 pressure)/98.1
                            $sensors[$id]->value = round( (($sensordata[0]['pressure_wap'] - $sensordata[0]['pressure_ssu'])/98.1), 2)." m";
                        }
                        else
                        {
                            $sensors[$id]->value = $sensordata[0]['value'];
                        }
                    }
                }
            }
        }

        return view('sensors.data',compact('sensors'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function showdata($id)
    {
        $item = Sensor::find($id);

        $client = new \Influx;
        $result = $client::query('SELECT * FROM "ssu","hap" WHERE sensor_id = \''.$item->key.'\' ORDER BY time DESC LIMIT 100');
        $data   = $result->getPoints();

        if ($data)
        {
            //die(print_r($data));
            $colors = ["#00252F", "#256581", "#FE4A34", "#FEA037", "#7ED321"];

            $datasets = [];
            $labels   = [];
            $color_i  = 0;
            foreach ($data[0] as $label => $value) 
            {
                $color = $colors[$color_i];
                $color_i = $color_i >= count($colors)-1 ? 0 : $color_i+1;
                if ($label != "time" && $label != "sensor_id" && $label != "type" && $label != "topic")
                {
                    array_push($datasets, [
                        "label"=>$label, 
                        "data"=>[],
                        "backgroundColor"=>"rgba(0,0,0,0)",
                        "borderColor"=>$color,
                        "pointBorderColor"=>$color,
                        "pointBackgroundColor"=>$color,
                        "pointHoverBackgroundColor"=>$color,
                        "pointHoverBorderColor"=>$color,
                    ]);
                }
            }

            foreach ($data as $point)
            {
                array_push($labels, $point["time"]);
                foreach ($point as $label => $value) 
                {
                    foreach ($datasets as $i => $dataset) 
                    {
                        if ($label == $dataset["label"])
                        {
                            array_push($datasets[$i]["data"], $value);
                            //die(print_r($datasets));
                        }
                    }
                }
            }
            //die(print_r($datasets));

            $chartjs = app()->chartjs
            ->name('lineChart')
            ->type('line')
            ->size(['width' => '100%', 'height' => '45%'])
            ->labels($labels)
            ->datasets($datasets)
            ->options([
                "datasets" => [
                    [
                        "fill" => false
                    ]],
                "scales" => [
                    "xAxes" => [[
                        "type" => 'time',
                        "time" => [
                            "unit" => 'minute',
                            "unitStepSize" => 60,
                            "displayFormats" => [
                                "minute" => 'Y-MM-DD HH:mm'
                            ]
                        ]
                    ]]
                ]
            ]);
        }
        else
        {
            $chartjs = app()->chartjs;
        }
        return view('sensors.showdata',compact('item','chartjs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = $this->sensorTypes;
        return view('sensors.create',compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'key' => 'required',
        ]);

        Sensor::create($request->all());

        return redirect()->route('sensors.index')
                        ->with('success','Sensor created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Sensor::find($id);
        return view('sensors.show',compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Sensor::find($id);
        $types = $this->sensorTypes;
        return view('sensors.edit',compact('item','types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'key' => 'required',
        ]);

        Sensor::find($id)->update($request->all());

        return redirect()->route('sensors.index')
                        ->with('success','Sensor updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Sensor::find($id)->delete();
        return redirect()->route('sensors.index')
                        ->with('success','Sensor deleted successfully');
    }
}