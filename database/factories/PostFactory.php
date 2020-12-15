<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use Faker\Generator as Faker;
use Carbon\Carbon;

$position = 1;
$factory->define(Post::class, function (Faker $faker) {

    static $position = 1;

    return [
        'user_id'	 => 1,
        'story_id'   => 1,
        'title'		 =>	$faker->text(6),
        'content'    => $faker->realText(200),
        'type'       => 'html',
        'active'     => 1,
        'position'	 => $position++,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});

$factory->state(App\Post::class, 'map', [
    'type' => 'map',
]);

$factory->state(App\Post::class, 'image', [
    'type' => 'image',
]);

