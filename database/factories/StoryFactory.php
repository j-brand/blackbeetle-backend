<?php

namespace Database\Factories;

use Storage;


use App\Models\Story;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Http\Traits\ImageTrait;

class StoryFactory extends Factory
{
    use ImageTrait;

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
            $story->path = "stories/{$story->id}/";

            $story->save();

            $titleImage = $this->saveImage(Storage::disk('public')->path("static/dummy/dummy_01.jpg"), $story->path, true);
            $this->genVariants($titleImage->id, 'story_title_image');
            $story->title_image =  $titleImage->id;
            $story->save();


            for ($i = 0; $i <= 20; $i++) {

                $rand = rand(0, 9);
                $postCount = Post::where('story_id', $story->id)->count();
                echo ("seeding post {$postCount} of Story {$story->title}\n");

                switch ($rand) {
                    case 0:
                    case 1:
                    case 2:
                        Post::factory()->imagePost()->create(['story_id' => $story->id, 'position' => $postCount  + 1]);
                        break;
                    case 3:
                        Post::factory()->mapPost()->create(['story_id' => $story->id, 'position' => $postCount  + 1]);
                        break;
                    case 4:
                        Post::factory()->videoPost()->create(['story_id' => $story->id, 'position' => $postCount  + 1]);
                        break;
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        Post::factory()->create(['story_id' => $story->id, 'position' => $postCount  + 1]);
                        break;
                    default:
                        break;
                }
            }
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
