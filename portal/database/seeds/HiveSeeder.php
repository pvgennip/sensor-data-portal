<?php

use Illuminate\Database\Seeder;
use App\Hive;

class HiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $item= new Hive;
        $item->user_id = 1;
        $item->type    = 'spaarkast';
        $item->name    = 'Beatrix';
        $item->save();

        $item= new Hive;
        $item->user_id = 1;
        $item->type    = 'spaarkast';
        $item->name    = 'Juliana';
        $item->save();

        $item= new Hive;
        $item->user_id = 2;
        $item->type    = 'spaarkast';
        $item->name    = 'Amalia';
        $item->save();
    }
}
