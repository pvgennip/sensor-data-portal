<?php
 
namespace App\Transformer;
use League\Fractal;
 
class SensorTransformer {
 
    public function transform($sensor) // works with array in stead of Collection (Object) 
    {
	    $valid_sensors = [
	    	't' => ['name'=>'temperature', 'unit'=>'°C'],
	    	'h' => ['name'=>'humidity', 'unit'=>'%RV'],
	    	'p' => ['name'=>'air_pressure', 'unit'=>'mbar'],
	    	'w' => ['name'=>'weight_sum', 'unit'=>''],
	    	'l' => ['name'=>'light', 'unit'=>'lux'],
	    	'bv' => ['name'=>'bat_volt', 'unit'=>'mV'],
	    	'weight_kg' => ['name'=>'weight_kg', 'unit'=>'Kg'],
	    ];

	    $name = $sensor['name'];
	    $valid= in_array($name, array_keys($valid_sensors));
	    
	    return [
            'name' 	=> $valid ? $valid_sensors[$name]['name'] : $sensor['name'],
            'value' => isset($sensor['value']) ? $sensor['value'] : "",
            'unit' 	=> $valid ? $valid_sensors[$name]['unit'] : "",
            'time'  => isset($sensor['time']) ? $sensor['time'] : 0,
            //'key'   => isset($sensor['key']) ? $sensor['key'] : ""
        ];
	    
	}
 
}