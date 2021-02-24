<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\Image;
use App\Models\Post;
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
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Story $story) {
            
            //
        })->afterCreating(function (Story $story) {
            Post::factory()->count(1)->mapPost()->create(['story_id' => $story->id]);
            Post::factory()->count(2)->create(['story_id' => $story->id]);
            Post::factory()->count(1)->imagePost()->create(['story_id' => $story->id]);
            Post::factory()->count(1)->videoPost()->create(['story_id' => $story->id]);
            Post::factory()->count(2)->create(['story_id' => $story->id]);
        });
    }


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
            'path'          => "stories/{$this->faker->unique()->randomDigit()}/",
            'slug'          => Str::slug($title_string),
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ];
    }
}
