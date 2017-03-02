<?php

use Illuminate\Database\Seeder;
use App\Sensor;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item= new Sensor;
        $item->user_id = 1;
        $item->hive_id = 1;
        $item->name    = 'Sensor test';
        $item->key     = '';
        $item->save();

        $item= new Sensor;
        $item->user_id = 1;
        $item->hive_id = 2;
        $item->name    = 'Sensor test';
        $item->key     = 'pim2';
        $item->save();

        $item= new Sensor;
        $item->user_id = 2;
        $item->hive_id = 3;
        $item->name    = 'Sensor test';
        $item->key     = 'marten1';
        $item->save();
    }
}
