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
            'title'      => $this->faker->text(6),
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
                'content' => '{"coordinates":[{"id":"Neu Delhi","position":{"lat":28.61584308390754,"lng":77.21540990624999},"tmp":"4"},{"id":"\r\nAgra","position":{"lat":27.17399791074048,"lng":78.01741185937499},"tmp":"4"},{"id":"Jaipur","position":{"lat":26.914695040529676,"lng":75.79268041406249},"tmp":"4"}],"zoomlevel":7,"connection":"0"}'
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
