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
        $item->name    = 'Sensor Pim 1';
        $item->key     = 'pim1';
        $item->save();

        $item= new Sensor;
        $item->name    = 'Sensor Pim 2';
        $item->key     = 'pim2';
        $item->save();

        $item= new Sensor;
        $item->name    = 'Sensor Marten 1';
        $item->key     = 'marten1';
        $item->save();
    }
}
