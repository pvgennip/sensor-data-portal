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
        $ssu_wap = [
            'SSU01'=>'0291c3a848135cae',
            'SSU02'=>'0ec03dbd60195cae',
            'SSU03'=>'1defc15d713a5fae',
            'SSU04'=>'27d9f81373155cae',
            'SSU05'=>'2b6ae9da49145cae',
            'SSU06'=>'2c0a568261345cae',
            'SSU07'=>'3d32354974225fae',
            'SSU08'=>'6d70a5d273205cac',
            'SSU09'=>'6d70a5d273205cad',
            'SSU10'=>'6d70a5d273205cae',
            'SSU11'=>'6d70a5d273205caf',
            'SSU12'=>'8637d86c6d1b5cae',
            'SSU13'=>'8b9d096877025cae',
            'SSU14'=>'9bc0993e5e385fae',
            'SSU15'=>'a71ce38c682c5cae',
            'SSU16'=>'ae285210771e5cae',
            'SSU17'=>'b25288714b125fae',
            'SSU18'=>'b254b62d73295cae',
            'SSU19'=>'b6ff57f54a125cae',
            'SSU20'=>'c8ebbb865f2c5fae',
            'SSU21'=>'cf69d7086c185cae',
            'SSU22'=>'cfed5920683e5cae',
            'SSU23'=>'d9159c754b3a5fae',
            'SSU24'=>'e1061a6f5e295fae',
            'SSU25'=>'f2f8cbfa4a395fae',
            'SSU26'=>'f7726a8d6e215cae',
            'SSU27'=>'fac0bc937c1d5cae',
        ];

        $hap_sum = [
            'HAP01'=>'c5a5f9d466745ae',
            'HAP02'=>'4cc2d987424445ae',
            'HAP03'=>'10666f47434345ae',
            'HAP04'=>'bbd44106c5645ae',
            'HAP05'=>'9bbe8bdb526645ae',
            'HAP06'=>'3a391c6a6d6f45ae',
            'HAP07'=>'8d2ddf6a6d5245ae',
            'HAP08'=>'18fb7292414745ae',
            'HAP09'=>'938bacd55c6645ae',
            'HAP10'=>'505b92c2524745ae',
            'HAP11'=>'5eaa3cb4534845ae',
            'HAP12'=>'a07e44a1416945ae',
            'HAP13'=>'5575bb8b6c4245ae',
            'HAP14'=>'18aa101b537d45ae',
            'HAP15'=>'29c3c1995d5745ae',
            'HAP16'=>'d9eca535454645ae',
            'HAP17'=>'d95c2b156d6245ae',
            'HAP18'=>'f12ffc50535e45ae',
            'HAP19'=>'4825e0db406045ae',
            'HAP20'=>'b0371b2475945ae',
            'HAP21'=>'',
            'HAP22'=>'d8db207a405345ae',
            'HAP23'=>'71a441b7b5545ae',
            'HAP24'=>'6acdaaa9525645ae',
            'HAP25'=>'27767a4b405145ae',
            'HAP26'=>'f637c2c56c6645ae',
            'HAP27'=>'508b6d37436645ae',
            'HAP28'=>'f7f9cdd96c6445ae',
            'HAP29'=>'2b48da49426645ae',
            'SUM01'=>'e2e648617646545a',
            'SUM02'=>'31397fb6735b555a',
            'SUM03'=>'2fe8fe347263555a',
            'SUM04'=>'5cae377a166b545a',
            'SUM05'=>'de7016b87053555a',
            'SUM06'=>'37c01b56737c555a',
            'SUM07'=>'adf51a77726b555a',
            'SUM08'=>'cf24923e5b65555a',
            'SUM09'=>'e43bc2eb5f42555a',
            'SUM10'=>'9292d177474a5a5a',
            'SUM11'=>'21b0ed0e7c63555a',
            'SUM12'=>'d18d5a6146685a5a',
            'SUM13'=>'f8aa2c2445535a5a',
            'SUM14'=>'4766ac7c7251555a',
            'SUM15'=>'8c28c6507065555a',
            'SUM16'=>'62ff69234b54545a',
            'SUM17'=>'b182f6207055555a',
            'SUM18'=>'6e928e49717b555a',
            'SUM19'=>'762f6e936c7c5a5a',
            'SUM20'=>'09cf11657364555a',
            'SUM21'=>'33c295ba65425a5a',
            'SUM22'=>'9a75c411775b555a',
            'SUM23'=>'ec8c80f64e65555a',
            'SUM24'=>'d2fa5b426748545a',
            'SUM25'=>'175c202a7366545a',
            'SUM26'=>'7547e06d7762555a',
            'SUM27'=>'f7d505d95b585a5a',
            'SUM28'=>'8cb88e847353555a',
            'SUM29'=>'6ff87211774c555a',
        ];

        foreach ($ssu_wap as $name => $mac) 
        {
            if (Sensor::where('key', $mac)->count() == 0)
            {
                $item= new Sensor;
                $item->name    = $name;
                $item->type    = 'ssu_wap';
                $item->key     = $mac;
                $item->save();
            }
        }

        foreach ($hap_sum as $name => $mac) 
        {
            if (Sensor::where('key', $mac)->count() == 0)
            {
                $item= new Sensor;
                $item->name    = $name;
                $item->type    = 'hap_sum';
                $item->key     = $mac;
                $item->save();
            }
        }

    }
}
