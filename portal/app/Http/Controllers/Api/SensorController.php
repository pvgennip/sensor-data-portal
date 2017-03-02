<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\User;
use App\Sensor;
use App\Setting;
use App\Transformer\SensorTransformer;
use InfluxDB;
use Response;
use EllipseSynergie\ApiResponse\Contracts\Response as TransformerResponse;

class SensorController extends Controller
{
    protected $respose;
    protected $influx_database;
    protected $valid_sensors = [
            't' => 'temperature',
            'h' => 'humidity',
            'p' => 'air_pressure',
            'w' => 'weight_sum',
            'l' => 'light',
            'bv' => 'bat_volt',
            'weight_kg' => 'weight_kg',
            'weight_kg_corrected' => 'weight_kg_corrected',
        ];
 
    public function __construct(TransformerResponse $response)
    {
        $this->response = $response;
        $this->influx_database();
    }
    
    
    protected function influx_database()
    {
        $influx_host= env('INFLUX_HOST', 'localhost');
        $influx_port= env('INFLUX_PORT', '8086');
        $influx_db  = env('INFLUX_DB', 'bee_data');
        $influx_user= env('INFLUX_USER', 'bee_data_queen');
        $influx_pass= env('INFLUX_PASS', 'ZxoDJ2fGft@aiCqUkBqjqCip');

        $client     = new InfluxDB\Client($influx_host, $influx_port, $influx_user, $influx_pass);
        $database   = $client->selectDB($influx_db);

        //die(print_r($client->listDatabases()));
        //die(print_r($client->listUsers()));

        if (!$database->exists()) {
            $database->create(new InfluxDB\Database\RetentionPolicy($influx_db, '14d', 1, true));
            $client->admin->createUser($influx_user, $influx_pass, \InfluxDB\Client\Admin::PRIVILEGE_ALL);
        }
        //die(print_r($database->listRetentionPolicies()));

        $this->influx_database = InfluxDB\Client::fromDSN(sprintf("influxdb://$influx_user:$influx_pass@%s:%s/%s",  $influx_host, $influx_port, $influx_db));
        
    }

    protected function get_user_sensor_from_ids(Request $request)
    {
        $user_id = $request->user()->id;
        
        if ($request->exists('hive_id'))
        {
            $hive_id = $request->input('hive_id');
            $check_sensor = Sensor::where('user_id', $user_id)->where('hive_id', $hive_id)->first();
        }
        else
        {
            $check_sensor = Sensor::where('user_id', $user_id)->first();
        }

        if(!$check_sensor)
             return Response::json('No key found for user', 404);

        return $check_sensor;
    }


    protected function convertSensorStringToArray($data_string)
    {
        $out = [];
        $arr = explode("|", $data_string);
        foreach ($arr as $str) 
        {
            $str_arr = explode(":",$str);
            if (count($str_arr) > 1)
                $out[$str_arr[0]] = $str_arr[1];

        }
        return $out;
    }

    protected function calculateWeightKg($data_array, $user_id, $hive_id)
    {
        $totalWeight = 0;
        //$log = [];
        foreach($data_array as $sensor => $value)
        {
            if (strpos($sensor, "w_") !== false && strlen($sensor) == 4)
            {
                $sensor_offset = Setting::where('user_id', $user_id)->where('hive_id', $hive_id)->where('name', $sensor)->first();
                $sensor_factor = Setting::where('user_id', $user_id)->where('hive_id', $hive_id)->where('name', $sensor.'_kg_per_val')->first();
                

                if ($sensor_offset) // offset available
                {
                    $factor = $sensor_factor ? floatval($sensor_factor->value) : 1;
                    $weight = (floatval($value) - floatval($sensor_offset->value)) * $factor;
                    $totalWeight += $weight;
                    //$log[] = ('user: '.$user_id.' hive_id:'.$hive_id.' sensor name: '.$sensor.' s='.$value.' s_o='.$sensor_offset->value.' f='.$factor.' w='.$weight.' tot='.$totalWeight);
                }
                else
                {
                    $totalWeight += floatval($value);
                }
            }

        }
        //die(print_r($log));
        return $totalWeight;
    }



    // Public functions

    public function index(Request $request)
    {
        $sensor = $this->get_user_sensor_from_ids($request);

        $result  = $this->influx_database->query('SELECT "name",* from "sensors" WHERE "key" = \''.$sensor->key.'\' AND time > now() - 365d GROUP BY "name" ORDER BY time DESC LIMIT 1');

        $sensors = $result->getPoints();
        
        return $this->response->withCollection($sensors, new SensorTransformer());
    }

    

    public function store(Request $request)
    {
        // Check for valid data 
        if ($request->exists('data')) // Check for sensor string (colon and pipe devided)
        {
            $data_array = $this->convertSensorStringToArray($request->input('data'));
            $provide_response_data = false; // save bandwidth with simple response
        }
        else // Assume post data input
        {
            $data_array = $request->input();
            $provide_response_data = true;
        }

        if (!in_array('key', array_keys($data_array)) )
            return Response::json('No key provided', 400);

        // Check if key is valid
        $sensor_key     = $data_array['key']; // save sensor data under sensor key
        $check_sensor   = Sensor::where('key', $sensor_key)->first();

        if(!$check_sensor)
             return Response::json('No valid key provided', 401);

        unset($data_array['key']);

        $sensor_user_id = $check_sensor->user_id;
        $sensor_hive_id = $check_sensor->hive_id;

        $weight_kg = $this->calculateWeightKg($data_array, $sensor_user_id, $sensor_hive_id);
        $data_array['weight_kg'] = $weight_kg;
        $data_array['weight_kg_corrected'] = $weight_kg + 0.0746 * $data_array['t'];

        // store posted data
        $sensors = [];
        foreach ($data_array as $key => $value) {
            
            if (in_array($key, array_keys($this->valid_sensors)) )
            {
                $sensor = new Sensor;
                $sensor->name   = $key;
                $sensor->value  = floatval($value);
                $sensor->time   = time();
                
                $points = array
                (
                    new InfluxDB\Point(
                        'sensors', // name of the measurement
                        $sensor->value, // the measurement value
                        ['key' => $sensor_key, 'name'=>$sensor->name], // optional tags
                        [], // optional additional fields
                        $sensor->time // Time precision has to be set to seconds!
                    )
                );

                $stored = $this->influx_database->writePoints($points, InfluxDB\Database::PRECISION_SECONDS);
                
                if ($stored)
                    $sensors[] = $sensor;
            }
        }

        if(count($sensors) > 0) 
        {
            return Response::json($provide_response_data ? $sensors : "saved");
        } 
        else
        {
            return Response::json('Could not create a sensor value', 500);
        }

    }

    public function data(Request $request, $name)
    {
        //Get the sensor
        $sensor  = $this->get_user_sensor_from_ids($request);
        
        $sensor_name = array_search($name, $this->valid_sensors);

        if ($sensor_name)
        {
            $result  = $this->influx_database->query('SELECT "name",* from "sensors" WHERE "key" = \''.$sensor->key.'\' AND "name" = \''.$sensor_name.'\' AND time > now() - 24h GROUP BY "name" ORDER BY time DESC LIMIT 1000');

            $sensors = $result->getPoints();

            if ($sensors) 
            {
                return $this->response->withCollection($sensors, new SensorTransformer());
            }
        }
        return Response::json('No sensor values', 500);
    }


}
