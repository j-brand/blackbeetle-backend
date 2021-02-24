<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        
        $role_admin         = Role::where('id', 1)->first();
        $role_gast          = Role::where('id', 2)->first();
       // $role_strolch       = Role::where('id', 3)->first();
        //$role_frechdachs    = Role::where('id', 4)->first();
        //$role_schlingel     = Role::where('id', 5)->first();

        $user = new User();
        $user->id = 1;
        $user->name = 'Johannes';
        $user->email = 'johannes@blackbeetle.de';
        $user->email_verified_at = now();
        $user->active = 1;
        $user->password = bcrypt('secret');
        $user->save();
        $user->roles()->attach($role_admin);

        $user = new User();
        $user->id = 69;
        $user->name = 'postman';
        $user->email = 'postman@blackbeetle.de';
        $user->email_verified_at = now();
        $user->active = 0;
        $user->password = bcrypt('secret');
        $user->save();

        $user = new User();
        $user->id = 33;
        $user->name = 'gast';
        $user->email = 'gast@blackbeetle.de';
        $user->email_verified_at = now();
        $user->active = 0;
        $user->password = bcrypt('secret');
        $user->save();
/*
        $user = new User();
        $user->id = 2;
        $user->name = 'Strolch';
        $user->email = 'strolch@blackbeetle.de';
        $user->verified = 1;
        $user->active = 1;
        $user->password = bcrypt('secret');
        $user->save();
        $user->roles()->attach($role_strolch);

        $user = new User();
        $user->id = 3;
        $user->name = 'Frechdachs';
        $user->email = 'frechdachs@blackbeetle.de';
        $user->verified = 1;
        $user->active = 1;
        $user->password = bcrypt('secret');
        $user->save();
        $user->roles()->attach($role_frechdachs);

        $user = new User();
        $user->id = 4;
        $user->name = 'Schlingel';
        $user->email = 'schlingel@blackbeetle.de';
        $user->verified = 1;
        $user->active = 1;
        $user->password = bcrypt('secret');
        $user->save();
        $user->roles()->attach($role_schlingel);
        */
    }
}
