<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;
use App\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // FIRST CREATE PERMISSIONS
        $perm_super = Permission::all();
        
        $perm_admin = Permission::where('name','role-list')
                              ->orWhere('name','user-list')
                              ->orWhere('name','user-create')
                              ->orWhere('name','user-edit')
                              ->orWhere('name','user-delete')
                              ->orWhere('name','group-list')
                              ->orWhere('name','group-create')
                              ->orWhere('name','group-edit')
                              ->orWhere('name','group-delete')
                              ->orWhere('name','sensor-list')
                              ->orWhere('name','sensor-create')
                              ->orWhere('name','sensor-edit')
                              ->orWhere('name','sensor-delete')
                              ->get();
                              
        $perm_manag = Permission::where('name','role-list')
                              ->orWhere('name','user-list')
                              ->orWhere('name','group-list')
                              ->orWhere('name','group-create')
                              ->orWhere('name','group-edit')
                              ->orWhere('name','group-delete')
                              ->orWhere('name','sensor-list')
                              ->orWhere('name','sensor-create')
                              ->orWhere('name','sensor-edit')
                              ->get();
                              

        // Roles
        if (Role::count() == 0)
        {
          $super = new Role();
          $super->name         = 'superadmin';
          $super->display_name = 'Super administrator'; // optional
          $super->description  = 'User is the master of the system, and can edit everything'; // optional
          $super->save();
          $super->attachPermissions($perm_super); // all roles
          
          $admin = new Role();
          $admin->name         = 'admin';
          $admin->display_name = 'Administrator'; // optional
          $admin->description  = 'User is allowed to manage users, groups and sensors'; // optional
          $admin->save();
          $admin->attachPermissions($perm_admin); // all roles

          $manag = new Role();
          $manag->name         = 'manager';
          $manag->display_name = 'Sensor manager'; // optional
          $manag->description  = 'User is allowed to manage groups and sensors'; // optional
          $manag->save();
          $manag->attachPermissions($perm_manag); // all roles
        }

        // Users
        if (User::count() == 0)
        {
          $user = new User();
          $user->name     = 'Pim';
          $user->email    = 'pim@iconize.nl';
          $user->password = bcrypt('password');
          $user->api_token= '000000000000000000000000000000000000000000000000000000000000';
          $user->remember_token = str_random(10);
          $user->save();
          $user->attachRole($super);

          $user = new User();
          $user->name     = 'Lars';
          $user->email    = 'lars.sjogreen@akvo.org';
          $user->password = bcrypt('password');
          $user->api_token= str_random(60);
          $user->remember_token = str_random(10);
          $user->save();
          $user->attachRole($super);

          $user = new User();
          $user->name     = 'Arthur';
          $user->email    = 'arthur@akvo.org';
          $user->password = bcrypt('password');
          $user->api_token= str_random(60);
          $user->remember_token = str_random(10);
          $user->save();
          $user->attachRole($super);

          $user = new User();
          $user->name     = 'Hans';
          $user->email    = 'hans@akvo.org';
          $user->password = bcrypt('password');
          $user->api_token= str_random(60);
          $user->remember_token = str_random(10);
          $user->save();
          $user->attachRole($admin);
        }

    }
}
