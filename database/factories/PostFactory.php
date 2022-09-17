<?php

namespace Database\Factories;

use Storage;

use App\Models\Post;
use App\Models\Story;
use App\Models\Comment;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Http\Traits\ImageTrait;

class PostFactory extends Factory
{

    use ImageTrait;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Post $post) {

            //
        })->afterCreating(function (Post $post) {

            if ($post->type == 'image') {

                $story = Story::find($post->story_id);

                collect([2, 3, 7, 8])->map(function ($i) use ($story, $post) {

                    $image = $this->saveImage(Storage::disk('public')->path("static/dummy/dummy_0{$i}.jpg"), "{$story->path}{$post->id}/", true);
                    $this->genVariants($image->id, 'image_post');


                    $image->description = $this->faker->realText(20);
                    $image->save();
                    $imageCount = $post->images->count();
                    $post->images()->attach($image->id, ['position' => $imageCount + 1]);
                });
            }

            //Create Comments
            $rand = rand(0, 5);
            Comment::factory()->count($rand)->create(['post_id' => $post->id]);
        });
    }




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
                'content' => '{"path":"storage\/static\/dummy","filename":"dummy_video.mp4"}'
            ];
        });
    }
}
