<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Album;
use App\Models\Image;

class AlbumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Album::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title_string = $this->faker->city();

        return [
            'title' => $title_string,
            'title_image' => Image::factory()->create(),
            'title_image_text' => $this->faker->realText(60),
            'start_date' => $this->faker->dateTimeBetween($startDate = '-4 years', $endDate = '-3 years', $timezone = null, $format = 'Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween($startDate = '-2 years', $endDate = '-1 years', $timezone = null, $format = 'Y-m-d'),
            'description' => $this->faker->realText(300),
            'slug' => Str::kebab($title_string),
            'active' => 1,
        ];
    }
}
