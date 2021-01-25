<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class StoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Story::class;


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title_string = $this->faker->text(6);

        return [
            'title'         => $title_string,
            'description'   => $this->faker->sentence(10),
            'title_image'   => Image::factory()->create(),
            'active'        => 1,
            'path'          => 'stories/1/',
            'slug'          => Str::slug($title_string),
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ];
    }
}
