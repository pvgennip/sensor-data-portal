<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\Controller;
use App\Sensor;
use Illuminate\Support\Facades\Storage;
use Moment\Moment;

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
        'depth_wap'=>[
            'name'=>'WAP Depth',
            'unit'=>'m'
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
            'name'=>'HAP 1-2 µm particles',
            'unit'=>'pcs/ft³'
        ],
        'p2'=>[
            'name'=>'HAP 3-10 µm particles',
            'unit'=>'pcs/ft³'
        ],
        'co'=>[
            'name'=>'HAP Carbon monoxide 1',
            'unit'=>'ppm'
        ],
        'co2'=>[
            'name'=>'HAP Carbon monoxide 2',
            'unit'=>'ppm'
        ],
        't_max'=>[
            'name'=>'SUM temperature max',
            'unit'=>'°C'
        ],
    ];

    protected function allowedSensors($request, $id=null)
    {
        $user = $request->user();
        if ($user->hasRole('superadmin'))
        {
            $sensors = isset($id) ? Sensor::findOrFail($id) : Sensor::all()->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE, false);
        }
        else
        {
            $sensors = isset($id) ? $user->sensors()->findOrFail($id) : $user->sensors()->get()->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE, false);
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
                    $lastMoment = new Moment(substr($sensordata[0]['time'],0,19), 'UTC');
                    $sensors[$id]->date = $lastMoment->setTimezone('Europe/Berlin')->format('Y-m-d H:i:s T');
                    if ($sensor->type == "ssu_wap" && isset($sensordata[0]['pressure_wap']) && isset($sensordata[0]['pressure_ssu']))
                    {
                        // Depth = Depth(m)=(WAP pressure- BME280 pressure)/98.1
                        $sensors[$id]->value = $this->sensorUnits['depth_wap']['name'].": ".round( (($sensordata[0]['pressure_wap'] - $sensordata[0]['pressure_ssu'])/98.1), 2)." ".$this->sensorUnits['depth_wap']['unit'];
                    }
                    else if ($sensor->type == "hap_sum" && isset($sensordata[0]['p1']))
                    {
                        // Depth = Depth(m)=(WAP pressure- BME280 pressure)/98.1
                        $sensors[$id]->value = $this->sensorUnits['p1']['name'].": ".$sensordata[0]['p1']." ".$this->sensorUnits['p1']['unit'];
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
        $sensor      = $this->allowedSensors($request, $id);
        
        $type   = substr($sensor->type, 0, 3);
        $client = new \Influx;
        $first  = $client::query('SELECT * FROM '.$type.' WHERE sensor_id = \''.$sensor->key.'\' ORDER BY time ASC LIMIT 1')->getPoints(); // get first sensor date
        
        if (count($first) == 0)
            return redirect()->route('sensors.data')->with('error','No chart data available for sensor '.$sensor->name);
        
        //$firstSensorMoment = new Moment(substr($first[0]['time'],0,10));
        
        $resolutions = [
            'sensor'=>'Sensor resolution',
            '1h'=>__('general.hour'),
            '1d'=>__('general.day'),
            '1w'=>__('general.week'),
            // '1M'=>__('general.month'),
            // '1y'=>__('general.year')
        ];

        $dateDisplayFormat  = 'd-m-Y';
        $dateInfluxFormat   = 'Y-m-d';
        $selectedFromMoment = new Moment();
        $selectedFromMoment = $selectedFromMoment->subtractDays(6)->startOf('day');
        $selectedToMoment   = new Moment();

        $selectedResolution = 'sensor';
        if ($request->has('resolution'))
        {
            $selectedResolution = $request->input('resolution')[0];
            $selectedDateRange  = $request->input('daterange');
            $selectedDateArray  = explode(' - ', $selectedDateRange);
            $fda                = explode('-', $selectedDateArray[0]); // based on formatting 'd-m-Y'
            $tda                = explode('-', $selectedDateArray[1]); // based on formatting 'd-m-Y'
            $selectedFromMoment = new Moment($fda[2].'-'.$fda[1].'-'.$fda[0]);
            $selectedToMoment   = new Moment($tda[2].'-'.$tda[1].'-'.$tda[0]);
            $selectedToMoment   = $selectedToMoment;
        }

        //$selectedFromMoment = $selectedFromMoment > $firstSensorMoment ? $selectedFromMoment : $firstSensorMoment; // Set from to first value, gets weird if to < from
        $selectedFromDate   = $selectedFromMoment->format($dateDisplayFormat);
        $selectedToDate     = $selectedToMoment->format($dateDisplayFormat);
        $selectedDateRange  = $selectedFromDate.' - '.$selectedToDate;

        $selectedToMoment->addDays(1)->startOf('day'); // Do this only for Influx, after the date display parsing

        $maxDataPoints      = 1440;
        $groupBySelect      = '*';
        $groupByResolution  = 'LIMIT '.$maxDataPoints;
        
        if($selectedResolution != 'sensor')
        {
            $groupBySelect     = 'MEAN(*)';
            $groupByResolution = 'GROUP BY time('.$selectedResolution.') LIMIT '.$maxDataPoints;
        }
        
        $query      = 'SELECT '.$groupBySelect.' FROM '.$type.' WHERE sensor_id = \''.$sensor->key.'\' AND time > \''.$selectedFromMoment->format($dateInfluxFormat).'\' AND time < \''.$selectedToMoment->format($dateInfluxFormat).'\' '.$groupByResolution;
        $result     = $client::query($query);

        $data       = $result->getPoints();
        $dataPoints = count($data);
        if ($dataPoints > 0)
        {
            //die(print_r($data));
            $colors = ["#00252F", "#256581", "#FE4A34", "#FEA037", "#7ED321", "#B310AA", "#700D47", "#9B5D18"];

            $datasets = [];
            $labels   = [];
            $color_i  = 0;
            $yAxes    = [
                [
                    "position" => 'left',
                    "id" => 'y1',
                    "scaleLabel" =>
                    [
                        "display" => false,
                        "labelArray"=>[],
                        "labelString" => "",
                    ],
                ],
                // [
                //     "position" => 'right',
                //     "id" => 'y2',
                //     "scaleLabel" =>
                //     [
                //         "display" => false,
                //         "labelArray"=>[],
                //         "labelString" => "",
                //     ],
                // ],
            ];

            foreach ($data[0] as $label => $value) 
            {
                $color = $colors[$color_i];
                $color_i = $color_i >= count($colors)-1 ? 0 : $color_i+1;
                if ($label != "time" && $label != "sensor_id" && $label != "type" && $label != "topic")
                {
                    // Label for axis and legend creation
                    if (array_key_exists($label, $this->sensorUnits))
                    {
                        $name   = $this->sensorUnits[$label]['name'];
                        $unit   = $this->sensorUnits[$label]['unit'];
                    }
                    else if (array_key_exists(str_replace("mean_", "", $label), $this->sensorUnits)) // also cover mean values
                    {
                        $name = $this->sensorUnits[str_replace("mean_", "", $label)]['name'];
                        $unit = $this->sensorUnits[str_replace("mean_", "", $label)]['unit'];
                    }
                    else
                    {
                        $name = ucfirst(str_replace("_", " ", $label));
                        $unit = '-';
                    }
                    
                    $legend = $name.' ('.$unit.')';

                    // Y-axis selection
                    $yAxisIndex = 0; //floatval($value) > 200 ? 0 : 1;
                    $yAxes[$yAxisIndex]['scaleLabel']['display'] = false; 
                    array_push($yAxes[$yAxisIndex]['scaleLabel']['labelArray'], $unit);
                    
                    $axisId = $yAxes[$yAxisIndex]['id'];

                    array_push($datasets, [
                        "labelId"=>$label,
                        "label"=>$legend,
                        "yAxisID"=>$axisId,
                        "data"=>[],
                        "backgroundColor"=>"rgba(0,0,0,0)",
                        "borderColor"=>$color,
                        "pointBorderColor"=>$color,
                        "pointBackgroundColor"=>$color,
                        "pointHoverBackgroundColor"=>$color,
                        "pointHoverBorderColor"=>$color,
                        "cubicInterpolationMode"=>'monotone',
                        "lineTension"=>0,
                    ]);
                }
            }

            foreach ($yAxes as $key => $axis) 
            {
                $yAxes[$key]['scaleLabel']['labelString'] = implode(', ',array_unique($axis['scaleLabel']['labelArray']));
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

            $chartjs = [];
            foreach ($datasets as $i => $dataset) 
            {
                array_push($chartjs, app()->chartjs
                    ->name('lineChart'.$i)
                    ->type('line')
                    //->size(['width' => '100%', 'height' => '80%'])
                    ->labels($labels)
                    ->datasets([$dataset])
                    ->options([
                        "animation"=>false,
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
                                        "year" => 'Y MMM D HH:mm',
                                        "month" => 'Y MMM D HH:mm',
                                        "quarter" => 'Y MMM D HH:mm',
                                        "week" => 'MMM D HH:mm',
                                        "day" => 'MMM D HH:mm',
                                        "hour" => 'MMM D HH:mm',
                                        "minute" => 'MMM D HH:mm',
                                        "second" => 'MMM D HH:mm:ss',
                                        "millisecond" => 'MMM D HH:mm:ss',
                                    ]
                                ]
                            ]],
                            "yAxes" => $yAxes
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
                    ])
                );
            }
            

            return view('sensors.showdata',compact('sensor','chartjs','resolutions','selectedResolution','selectedDateRange','dataPoints','maxDataPoints'));
        }
        else
        {
            $error = 'No chart data available for sensor '.$sensor->name.' within date range '.$selectedDateRange;
            return view('sensors.showdata',compact('sensor','chartjs','resolutions','selectedResolution','selectedDateRange','dataPoints','maxDataPoints','error'));
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
        $messages = [
            'key.unique'  => 'The Device ID must be unique, this one is already taken. Lowercase and uppercase ID\'s are regarded as equal.',
        ];

        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'key' => 'required|unique:sensors,key',
        ], $messages);

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
        $messages = [
            'key.unique'  => 'The Device ID must be unique, this one is already taken. Lowercase and uppercase ID\'s are regarded as equal.',
        ];

        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'key' => 'required|unique:sensors,key,'.$id,
        ], $messages);

        $this->allowedSensors($request, $id)->update($request->all());
        return redirect()->route('sensors.index')
                            ->with('success','Sensor updated successfully');
        
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