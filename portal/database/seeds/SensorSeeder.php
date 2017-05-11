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
        $item->name    = 'SSU test';
        $item->type    = 'ssu_wap';
        $item->key     = '6d70a5d273205caf';
        $item->save();

        $item= new Sensor;
        $item->name    = 'HAP test';
        $item->type    = 'hap_sum';
        $item->key     = '0000E0DB40604500';
        $item->save();
    }
}
