<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Album;
use Faker\Generator as Faker;

$factory->define(Album::class, function (Faker $faker) {

    $title_string = $faker->city();

    return [
        'title' => $title_string,
        'title_image' => function () {
            return factory(App\Image::class)->create([
                'width' => '500',
                'height' => '300',
            ])->id;
        },
        'title_image_text' => $faker->realText(60),
        'start_date' => $faker->dateTimeBetween($startDate = '-4 years', $endDate = '-3 years', $timezone = null, $format = 'Y-m-d'),
        'end_date' => $faker->dateTimeBetween($startDate = '-2 years', $endDate = '-1 years', $timezone = null, $format = 'Y-m-d'),
        'description' => $faker->realText(300),
        'slug' => Str::kebab($title_string),
        'active' => 1,
    ];
});
