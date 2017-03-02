<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use App\Setting;
use App\Hive;

class SettingController extends Controller
{
    public function store(Request $request)
    {
    	$user_id = $request->user()->id;
        $hive_id = $request->input('hive_id');
        $type    = $request->input('type');
            
        $hive_cnt= Hive::where('user_id', $user_id)->count();

        $hive    = Hive::firstOrCreate(['user_id'=>$user_id], ['id'=>$hive_id, 'type'=>'spaarkast', 'name'=>'Kast '.($hive_cnt+1) ]);

        $save_cnt = 0;
        foreach($request->input() as $name => $value)
        {
            if ($name == "")
                continue;

    		$setting = Setting::updateOrCreate(['user_id'=>$user_id, 'hive_id'=>$hive->id, 'name'=>$name], ['type'=>$type, 'value'=>$value]);
            
            if ($setting)
                $save_cnt++;

        }

        if ($save_cnt > 0)
        {
    	   return $this->index($request);
        }
        else
        {
            return Response::json('no named settings to save', 400);
        }
    }
    
    public function index(Request $request)
    {
    	$user_id = $request->user()->id;
    	
    	$settings= Setting::where('user_id', $user_id)
    					->orderBy('type', 'asc')
			    		->orderBy('name', 'asc')
			    		->get(['type','name','value']);

    	return Response::json($settings);
    }

}
