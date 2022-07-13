<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Http\Traits\ImageTrait;


use App\Models\Album;
use App\Models\Image;
use Storage;


class AlbumFactory extends Factory
{

    use ImageTrait;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Album::class;


    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Album $album) {
        })->afterCreating(function (Album $album) {

            $album->path = "albums/{$album->id}/";

            $album->save();
            collect([2, 3, 4, 5, 2, 3, 4, 5, 2, 3, 4, 5])->map(function ($i) use ($album) {

                $image = $this->saveImage(Storage::disk('public')->path("static/dummy/dummy_0{$i}.jpg"), $album->path, true);
                $this->genVariants($image->id, 'album_image');

                $image->description = $this->faker->realText(20);
                $image->save();
                $imageCount = $album->images->count();
                $album->images()->attach($image->id, ['position' => $imageCount + 1]);
            });

            $titleImage = $this->saveImage(Storage::disk('public')->path("static/dummy/dummy_01.jpg"), $album->path, true);
            $this->genVariants($titleImage->id, 'album_title_image');

            $album->title_image =  $titleImage->id;
            $album->save();
        });
    }


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title_string = $this->faker->city();


        return [
            'title'             => $title_string,
            'title_image'       => 1,
            'title_image_text'  => $this->faker->realText(60),
            'start_date'        => $this->faker->dateTimeBetween($startDate = '-4 years', $endDate = '-3 years', $timezone = null, $format = 'Y-m-d'),
            'end_date'          => $this->faker->dateTimeBetween($startDate = '-2 years', $endDate = '-1 years', $timezone = null, $format = 'Y-m-d'),
            'description'       => $this->faker->realText(300),
            'path'              => "",
            'slug'              => Str::kebab($title_string),
            'active'            => 1,
        ];
    }
}
