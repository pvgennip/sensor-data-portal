<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'cors'], function()
{    

	Route::group(['middleware'=>'auth:api'], function()
	{  

		// Authenticate and provide the token
		Route::post('authenticate', 'Api\UserController@authenticate');

		// get list of sensors
		Route::get('sensors', 'Api\SensorController@index');

		// get more data of 1 sensors
		Route::get('sensors/{name}', 'Api\SensorController@data');


		// save hive 
		Route::post('hive', 'Api\HiveController@store');
		// get settings
		Route::get('hives', 'Api\HiveController@index');



		// save setting 
		Route::post('settings', 'Api\SettingController@store');
		// get settings
		Route::get('settings', 'Api\SettingController@index');

	});


	// Create and provide the token
	Route::post('register', 'Api\UserController@register');
	
	// Login and provide the token
	Route::post('login', 'Api\UserController@login');

	// save sensor data of multiple sensors
	Route::post('sensors', 'Api\SensorController@store');

	// save sensor data of multiple sensors (unsecure)
	Route::post('unsecure_sensors', 'Api\SensorController@store');


});
