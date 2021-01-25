<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Story;
use Carbon\Carbon;

use Faker\Generator as Faker;



$factory->define(Story::class, function (Faker $faker ,$params) {

    $title_string = $faker->text(6);


    return [
        'title'         => $title_string,
        'description'   => $faker->sentence(10),
        'title_image' => function () {
            return factory(App\Image::class)->create([
                'width' => '700',
                'height' => '300',
            ])->id;
        },
        'active'        => 1,
        'path'          => 'uploads/stories/1/',
        'slug'          => Str::slug($title_string),
        'created_at'    => Carbon::now(),
        'updated_at'    => Carbon::now(),
    ];
});
