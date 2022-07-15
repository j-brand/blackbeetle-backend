<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Seeder;

use App\Models\Story;
use App\Models\Album;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Storage::disk('public')->deleteDirectory('albums');
        Storage::disk('public')->deleteDirectory('stories');

        $this->call([
            OptionsTableSeeder::class,
            RolesTableSeeder::class,
            UsersTableSeeder::class,
        ]);

        Album::factory()->count(3)->create();
        Story::factory()->count(1)->create();
    }
}
