<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //Storage::disk('public')->deleteDirectory('images/test/');

        $this->call([
            OptionsTableSeeder::class,
            RolesTableSeeder::class,
            UsersTableSeeder::class,
        ]);

        /* 
        factory(factory(App\Image::class)->create([
            'width' => '25',
            'height' => '12',
            'title' => 'blur.jpg',
        ]));

        factory(App\Album::class, 3)->create()->each(function ($album) {

            $album->images()->saveMany(factory(App\Image::class, 3)->make([
                'width' => '300',
                'height' => '400',
            ]));

            $album->images()->saveMany(factory(App\Image::class, 3)->make([
                'width' => '300',
                'height' => '200',
            ]));

            $album->images()->saveMany(factory(App\Image::class, 3)->make([
                'width' => '400',
                'height' => '400',
            ]));
        });

        factory(App\Story::class, 4)->create()->each(function ($story) {

            factory(App\Post::class, 1)->create([
                'story_id' => $story->id,
            ]);

            factory(App\Post::class, 1)->states('map')->create([
                'story_id' => $story->id,
                'content' => '{"coordinates":[{"id":"Neu Delhi","position":{"lat":28.61584308390754,"lng":77.21540990624999},"tmp":"4"},  				{"id":"\r\nAgra","position":{"lat":27.17399791074048,"lng":78.01741185937499},"tmp":"4"},
                {"id":"Jaipur","position":{"lat":26.914695040529676,"lng":75.79268041406249},"tmp":"4"}],
                            "zoomlevel":7,"connection":"0"}',
            ]);

            factory(App\Post::class, 1)->states('image')->create([
                'story_id' => $story->id,
            ])->each(function ($post) {
                $post->images()->saveMany(factory(App\Image::class, 4)->make([
                    'width' => '900',
                    'height' => '300',
                ]));
            });

        }); */
    }
}
