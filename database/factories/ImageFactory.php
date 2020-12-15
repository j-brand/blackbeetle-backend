<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Image;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Image::class, function (Faker $faker, $params) {

    $width = '500';
    $height = '500';

    $date = new DateTime();
    $fileName = $date->getTimestamp() . '.jpg';

    $path = 'images/test/';

    if (array_key_exists('width', $params) && array_key_exists('height', $params)) {
        $width = $params['width'];
        $height = $params['height'];
    }

    if(array_key_exists('title', $params)){
        $fileName = $params['title'];
    }
    
    $url = 'https://picsum.photos/' . $width . '/' . $height;



    $file = Storage::disk('public')->put($path . $fileName, file_get_contents($url));

    return [

        'title' => $faker->unixTime($max = 'now'),
        'description' => $faker->sentence(8),
        'path' => url('storage/' . $path . $fileName),
        'extension' => 'jpg',
        'created_at' => Carbon::now(),

    ];
});
