<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Factories\Sequence;

use Illuminate\Database\Seeder;
use App\Models\Story;
use App\Models\Image;
use App\Models\Post;
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

        //Storage::disk('public')->deleteDirectory('images/test/');

        $this->call([
            OptionsTableSeeder::class,
            RolesTableSeeder::class,
            UsersTableSeeder::class,
        ]);

        Album::factory()->has(Image::factory()->count(3), 'images')->count(3)->create();
        // Story::factory()->hasPosts(3, )->create();
        Story::factory()->has(
            Post::factory()
                ->count(10)
                ->state(
                    new Sequence(
                        ['type' => 'html'],
                        ['type' => 'image'],
                        [
                            'type' => 'map',
                            'content' => '{"coordinates":[{"id":"Neu Delhi","position":{"lat":28.61584308390754,"lng":77.21540990624999},"tmp":"4"},{"id":"\r\nAgra","position":{"lat":27.17399791074048,"lng":78.01741185937499},"tmp":"4"},{"id":"Jaipur","position":{"lat":26.914695040529676,"lng":75.79268041406249},"tmp":"4"}],"zoomlevel":7,"connection":"0"}'
                        ],
                        ['type' => 'video']
                    )
                )
        )->count(3)->create();

        /*     
        factory(factory(Image::class)->create([
            'width' => '25',
            'height' => '12',
            'title' => 'blur.jpg',
        ]));

        factory(Album::class, 3)->create()->each(function ($album) {

            $album->images()->saveMany(factory(Image::class, 3)->make([
                'width' => '300',
                'height' => '400',
            ]));

            $album->images()->saveMany(factory(Image::class, 3)->make([
                'width' => '300',
                'height' => '200',
            ]));

            $album->images()->saveMany(factory(Image::class, 3)->make([
                'width' => '400',
                'height' => '400',
            ]));
        });

        factory(Story::class, 4)->create()->each(function ($story) {

            factory(Post::class, 1)->create([
                'story_id' => $story->id,
            ]);

            factory(Post::class, 1)->states('map')->create([
                'story_id' => $story->id,
                'content' => '{"coordinates":[{"id":"Neu Delhi","position":{"lat":28.61584308390754,"lng":77.21540990624999},"tmp":"4"},  				{"id":"\r\nAgra","position":{"lat":27.17399791074048,"lng":78.01741185937499},"tmp":"4"},
                {"id":"Jaipur","position":{"lat":26.914695040529676,"lng":75.79268041406249},"tmp":"4"}],
                            "zoomlevel":7,"connection":"0"}',
            ]);

            factory(Post::class, 1)->states('image')->create([
                'story_id' => $story->id,
            ])->each(function ($post) {
                $post->images()->saveMany(factory(App\Image::class, 4)->make([
                    'width' => '900',
                    'height' => '300',
                ]));
            });

        });  */
    }
}
