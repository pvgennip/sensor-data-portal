<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\Controller;
use App\Sensor;
use Illuminate\Support\Facades\Storage;

class SensorController extends Controller
{
    
    protected $sensorTypes = [
        'hap_sum'=>'Household Air Pollution (HAP) & Stove Usage Monitoring (SUM)',
        'ssu_wap'=>'Standard Surface Unit (SSU) & Water Pressure unit (WAP)',
        'Other'=>'Other'
    ];

    protected $sensorUnits = [
        'temp_ssu'=>[
            'name'=>'SSU temperature',
            'unit'=>'°C'
        ],
        'temp_wap'=>[
            'name'=>'WAP temperature',
            'unit'=>'°C'
        ],
        'pressure_ssu'=>[
            'name'=>'SSU pressure',
            'unit'=>'mbar'
        ],
        'pressure_wap'=>[
            'name'=>'WAP pressure',
            'unit'=>'mbar'
        ],
        'bat_v'=>[
            'name'=>'SSU Battery voltage',
            'unit'=>'V'
        ],
        'hap_bat_v'=>[
            'name'=>'HAP Battery voltage',
            'unit'=>'V'
        ],
        'sum_bat_v'=>[
            'name'=>'SUM Battery voltage',
            'unit'=>'V'
        ],
        'p1'=>[
            'name'=>'1-2 µm particles',
            'unit'=>'pcs/ft³'
        ],
        'p2'=>[
            'name'=>'3-10 µm particles',
            'unit'=>'pcs/ft³'
        ],
        'co'=>[
            'name'=>'Carbon monoxide',
            'unit'=>'ppm'
        ],
        'co2'=>[
            'name'=>'Carbon dioxide',
            'unit'=>'ppm'
        ],
    ];


    protected function allowedSensors($request, $id=null)
    {
        $user = $request->user();
        if ($user->hasRole('superadmin'))
        {
            $sensors = isset($id) ? Sensor::findOrFail($id) : Sensor::orderBy('name','ASC')->get();
        }
        else
        {
            $sensors = isset($id) ? $user->sensors()->findOrFail($id) : $user->sensors()->orderBy('name','ASC')->get();
        }
        return $sensors;
    }


    protected function convertSensorTypeToInfluxSeries($type)
    {
        return substr(strtolower($type), 0, 3); // convert hap_sum -> hap, ssu_wap -> ssu
    }


    protected function convertInfluxDataToCsv($data, $separator)
    {
        $csv_arr = [];
        array_push($csv_arr, implode($separator, array_keys($data[0]) ));
        foreach ($data as $key => $row) 
        {
            array_push($csv_arr, implode($separator, array_values($row) ));
        }
        return implode("\r\n", $csv_arr);
    }


    protected function addLastSensorData($sensors)
    {
        // Add data from influxdb
        $client = new \Influx;
        foreach ($sensors as $id => $sensor) 
        {
            $type = $this->convertSensorTypeToInfluxSeries($sensor->type);
            $result = $client::query('SELECT * FROM "'.$type.'" WHERE "sensor_id" = \''.$sensor->key.'\' AND time > now() - 365d GROUP BY "sensor_id" ORDER BY time DESC LIMIT 1');
            //die(print_r($result));
            if ($result)
            {
                $sensordata = $result->getPoints();
                if (count($sensordata) > 0)
                {
                    //die(print_r($sensordata));
                    $sensors[$id]->date  = $sensordata[0]['time'];
                    if ($sensor->type == "ssu_wap" && isset($sensordata[0]['pressure_wap']) && isset($sensordata[0]['pressure_ssu']))
                    {
                        // Depth = Depth(m)=(WAP pressure- BME280 pressure)/98.1
                        $sensors[$id]->value = round( (($sensordata[0]['pressure_wap'] - $sensordata[0]['pressure_ssu'])/98.1), 2)." m";
                    }
                    else if ($sensor->type == "hap_sum" && isset($sensordata[0]['p1']))
                    {
                        // Depth = Depth(m)=(WAP pressure- BME280 pressure)/98.1
                        $sensors[$id]->value = $sensordata[0]['p1']." pcs/ft³";
                    }
                    else if (isset($sensordata[0]['value']))
                    {
                        $sensors[$id]->value = $sensordata[0]['value'];
                    }
                }
            }
        }
        return $sensors;
    }

    protected function addCsvDataFile($sensors)
    {
        // Add data from influxdb
        $client = new \Influx;
        foreach ($sensors as $id => $sensor) 
        {
            $type = $this->convertSensorTypeToInfluxSeries($sensor->type);
            $result = $client::query('SELECT * FROM "'.$type.'" WHERE "sensor_id" = \''.$sensor->key.'\' ORDER BY time DESC');
            //die(print_r($result));
            if ($result)
            {
                $sensordata = $result->getPoints();
                if (count($sensordata) > 0)
                {
                    $csv   = $this->convertInfluxDataToCsv($sensordata, ",");
                    $file  = 'akvo-sensordata-'.$sensor->key.'-'.time().'.csv';
                    $saved = Storage::disk('public')->put($file, $csv);
                    if ($saved)
                    {
                        $link  = asset('storage/'.$file);
                        $sensors[$id]->link = $link;
                    }
                }
            }
        }
        return $sensors;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sensors = $this->allowedSensors($request);
            
        if (count($sensors) > 0)
        {
            return view('sensors.index',compact('sensors'));
        }
        else
        {
            return view('sensors.index')->with('error','You do not have access to any sensor yet, create one, or request access');
        }
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
        $sensors = $this->allowedSensors($request);
        
        $sensors = $this->addLastSensorData($sensors);

        // $sensors = $sensors->sortBy(function ($sensor, $key) {
        //                 return $sensor['date'];
        //             });

        return view('sensors.data',compact('sensors'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function showdata(Request $request, $id)
    {
        $item = $this->allowedSensors($request, $id);

        $type   = substr($item->type, 0, 3);
        $client = new \Influx;
        $first  = $client::query('SELECT * FROM '.$type.' WHERE sensor_id = \''.$item->key.'\' ORDER BY time ASC LIMIT 1')->getPoints();
        
        if (count($first) == 0)
            return redirect()->route('sensors.data')->with('error','No chart data available for sensor '.$item->name);
        
        $firstTm= $first[0]['time'];
        $query  = 'SELECT MEAN(*) FROM '.$type.' WHERE sensor_id = \''.$item->key.'\' AND time > \''.$firstTm.'\' GROUP BY time(1h) LIMIT 1000';
        $result = $client::query($query);

        $data   = $result->getPoints();

        if (count($data) > 0)
        {
            //die(print_r($data));
            $colors = ["#00252F", "#256581", "#FE4A34", "#FEA037", "#7ED321", "#B310AA", "#700D47", "#9B5D18"];

            $datasets = [];
            $labels   = [];
            $color_i  = 0;
            foreach ($data[0] as $label => $value) 
            {
                $color = $colors[$color_i];
                $color_i = $color_i >= count($colors)-1 ? 0 : $color_i+1;
                if ($label != "time" && $label != "sensor_id" && $label != "type" && $label != "topic")
                {
                    if (array_key_exists($label, $this->sensorUnits))
                    {
                        $legend = $this->sensorUnits[$label]['name'].' ('.$this->sensorUnits[$label]['unit'].')';
                    }
                    else if (array_key_exists(str_replace("mean_", "", $label), $this->sensorUnits)) // also cover mean values
                    {
                        $legend = $this->sensorUnits[str_replace("mean_", "", $label)]['name'].' ('.$this->sensorUnits[str_replace("mean_", "", $label)]['unit'].')';
                    }
                    else
                    {
                        $legend = ucfirst(str_replace("_", " ", $label));
                    }

                    array_push($datasets, [
                        "labelId"=>$label,
                        "label"=>$legend,
                        "yAxisID"=>$value > 200 ? "y2" : "y1",
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
                        if ($label == $dataset["labelId"])
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
            //->size(['width' => '100%', 'height' => '80%'])
            ->labels($labels)
            ->datasets($datasets)
            ->options([
                "maintainAspectRatio"=>false,
                "responsive"=>true,
                "datasets" => [
                    [
                        "fill" => false
                    ]],
                "scales" => [
                    "xAxes" => [[
                        "type" => 'time',
                        "time" => [
                            "displayFormats" => [
                                "year" => 'Y',
                                "month" => 'Y MMM',
                                "week" => 'Y MMM D',
                                "day" => 'Y MMM D',
                                "hour" => 'MMM D HH:mm',
                                "minute" => 'MMM D HH:mm',
                                "second" => 'MMM D HH:mm',
                            ]
                        ]
                    ]],
                    "yAxes" => [
                        [
                            "position" => 'left',
                            "id" => 'y1'
                        ],
                        [
                            "position" => 'right',
                            "id" => 'y2'
                        ],
                    ]
                ],
                "legend"=>[
                    "labels"=>[
                        //"fontFamily"=>"'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif",
                        "usePointStyle"=>true
                    ]
                ],
                // "tooltips"=>[
                //     "callbacks"=>[
                //         "title"=>'function(Array[tooltipItem], data) {
                //             console.log(tooltipItems.datasetIndex);
                //             return data.datasets[tooltipItems.datasetIndex].label;
                //         }'
                //     ]
                // ]
            ]);
            return view('sensors.showdata',compact('item','chartjs'));
        }
        else
        {
            return redirect()->route('sensors.data')->with('error','No chart data available for sensor '.$item->name);
        }
    }



    // Export
    // Show list of sensors
    public function export(Request $request)
    {
        $sensors = $this->allowedSensors($request);
        
        $sensors = $this->addLastSensorData($sensors);

        if ($request->has('selected'))
        {
            $selected = $request->input('selected');
            $data_sensors = $sensors->whereIn('id', $selected);
            $data_sensors = $this->addCsvDataFile($data_sensors);
        }

        return view('sensors.export',compact('sensors','data_sensors'));
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

        $sensor = Sensor::create($request->all());
        $request->user()->sensors()->attach($sensor);

        return redirect()->route('sensors.index')
                        ->with('success','Sensor created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $item = $this->allowedSensors($request, $id);
        return view('sensors.show',compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $item = $this->allowedSensors($request, $id);
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

        $already_exists = Sensor::where('id', '!=', $id)->where('key', $request->key)->get()->count() > 0 ? true : false;

        if ($already_exists == false)
        {
            $this->allowedSensors($request, $id)->update($request->all());
            return redirect()->route('sensors.index')
                            ->with('success','Sensor updated successfully');
        }
        else
        {
            return redirect()->route('sensors.index')
                            ->with('error','Sensor not updated, '.__('crud.key').' already exists and has to be unique!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->allowedSensors($request, $id)->delete();
        return redirect()->route('sensors.index')
                        ->with('success','Sensor deleted successfully');
    }
}