<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'    => 1,
            'story_id'   => 1,
            'title'      =>    $this->faker->text(6),
            'content'    => $this->faker->realText(200),
            'type'       => 'html',
            'date'       => Carbon::now(),
            'active'     => 1,
            'position'   => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
    public function mapPost()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'map',
            ];
        });
    }
    public function imagePost()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'image',
            ];
        });
    }
    public function videoPost()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'video',
            ];
        });
    }
}
