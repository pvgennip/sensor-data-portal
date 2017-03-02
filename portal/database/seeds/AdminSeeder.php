<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = ['name' => 'user', 'email' => 'pim@iconize.nl', 'password' => bcrypt('password'), 'api_token' => '000000000000000000000000000000000000000000000000000000000000', 'remember_token' => str_random(10)];
        $db = DB::table('users')->insert($user);
    }
}
