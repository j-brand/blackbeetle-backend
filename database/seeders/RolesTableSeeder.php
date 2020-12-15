<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('roles')->insert([
			'id'   => 1,
			'name' => 'admin',
		]);

		DB::table('roles')->insert([
			'id'   => 2,
			'name' => 'gast',
		]);

		DB::table('roles')->insert([
			'id'   => 3,
			'name' => 'strolch',
		]);

		DB::table('roles')->insert([
			'id'   => 4,
			'name' => 'frechdachs',
		]);

		DB::table('roles')->insert([
			'id'   => 5,
			'name' => 'schlingel',
		]);

    }
}
