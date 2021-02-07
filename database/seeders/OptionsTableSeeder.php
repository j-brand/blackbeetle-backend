<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('options')->insert([
            'option'    => 'my_location',
            'content'   => '{"position":{"lat":-29.594980,"lng":135.728591},"info":"Australien"}',
        ]);
    }
}
