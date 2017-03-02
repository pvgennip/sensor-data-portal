<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	protected $fillable = ['user_id', 'hive_id', 'type', 'name', 'value'];
	protected $hidden 	= [];

	public static function get_by_user_id($user_id)
	{
		$settings = self::where('user_id', $user_id)->first();
		
		// create new settings
		if(count($settings) == 0)
		{
			$settings = new Setting;
			$settings->user_id = $user_id;
			$settings->save();
		}

		return $settings;
	}
}
