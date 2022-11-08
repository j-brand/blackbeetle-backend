<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Story;
use App\Models\Album;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Storage;

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

        Album::factory()->count(2)->create();
        Story::factory()->count(2)->create();
        Subscriber::factory()->count(10)->create();

    }
}
