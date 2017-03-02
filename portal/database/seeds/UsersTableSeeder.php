<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user= new User;
        $user->name = 'Pim';
        $user->email = 'pim@iconize.nl';
        $user->password = bcrypt('password');
        $user->api_token = '000000000000000000000000000000000000000000000000000000000000';
        $user->remember_token = str_random(10);
        $user->save();

        $user= new User;
        $user->name = 'Marten';
        $user->email = 'mjl.schoonman@gmail.com';
        $user->password = bcrypt('password');
        $user->api_token = str_random(60);
        $user->remember_token = str_random(10);
        $user->save();
    }
}
