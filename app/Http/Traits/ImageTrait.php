<?php

namespace App\Http\Traits;

use App\Models\Image;

use IntImage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

trait ImageTrait
{
    private $sizes = array(
        0   =>  array('width' => 'null', 'height' => 'null', 'slug' => '',         'method' => false),
        1   =>  array('width' => 1400,   'height' => 'null', 'slug' => '_large',   'method' => false),
        2   =>  array('width' => 650,    'height' => 'null', 'slug' => '_thn',     'method' => false),
        /* STORY, ALBUM - title image*/
        3   =>  array('width' => 'null', 'height' => 300,    'slug' => '_title',   'method' => false),
        /* STORY - title thumbnail image*/
        4   =>  array('width' => 650,    'height' => 366,    'slug' => '_sthn',    'method' => 'fit'),
        /* ALBUM - title thumbnail image*/
        5   =>  array('width' => 500,    'height' => 500,    'slug' => '_athn',    'method' => 'fit'),
        /* ALBUM - gallery slider*/
        6   =>  array('width' => 1000,   'height' => 500,    'slug' => '_aslider', 'method' => 'fit'),
        61   =>  array('width' => 500,   'height' => 250,    'slug' => '_assmall', 'method' => 'fit'),
        7   =>  array('width' => 1920,   'height' => 1080,   'slug' => '_hd',      'method' => 'fit'),
        /* FACEBOOK SHARE IMAGE */
        8   =>  array('width' => 1200,   'height' => 630,    'slug' => '_fb',      'method' => 'fit'),
        9   =>  array('width' => 'null', 'height' => 50,     'slug' => '_thumb',   'method' => false),
        /* LAZY LOAD BLUR IMAGE */
        10 =>   array('width' => 60,     'height' => 'null', 'slug' => '_lazy',    'method' => false),
        11 =>   array('width' => 800,    'height' => 'null', 'slug' => '_aswipe',  'method' => false),
    );

    private $sizeConf = array(
        'album_image'       => [1, 2, 9],
        'album_title_image' => [2, 3, 4, 5, 6, 61, 7, 9],
        'story_title_image' => [2, 3, 4, 6, 8],
        'image_post'        => [1, 2, 9, 11],
    );

    private function resize($image, $size)
    {
        if ($this->sizes[$size]['method'] == 'fit') {

            return $image->fit($this->sizes[$size]['width'], $this->sizes[$size]['height'], function ($c) {
                $c->upsize();
            });
        } else {
            return $image->resize($this->sizes[$size]['width'], $this->sizes[$size]['height'], function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });
        }
    }


    public function saveImage($image, $path, $isPath = false)
    {
        $filename = uniqid('img_');
        $extension = $isPath ? pathinfo($image, PATHINFO_EXTENSION) : $image->getClientOriginalExtension();

        $filePath = "public/{$path}{$filename}.{$extension}";

        $imageFile = IntImage::make($image);
        Storage::put($filePath, $imageFile->encode(), 'public');




        $imageObj               = new Image();
        $imageObj->title        = $filename;
        $imageObj->path         = $path;
        $imageObj->width        = $imageFile->width();
        $imageObj->height       = $imageFile->height();
        $imageObj->description  = "";
        $imageObj->extension    = $extension;
        $imageObj->created_at   = Carbon::now();
        $imageObj->save();

        return $imageObj;
    }

    public function generateLazy($image)
    {
        $image->resize(60, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->blur(15);
        return $image;
    }

    public function deleteImageFile($id, $variantsOnly = false)
    {
        $image = Image::find($id);

        $collection = collect($this->sizes);

        if ($variantsOnly) {
            $collection = $collection->except(['0']);
        }

        $collection->map(function ($size) use ($image) {
            Storage::delete("public/{$image->path}{$image->title}{$size['slug']}.{$image->extension}");
            Storage::delete("public/{$image->path}{$image->title}{$size['slug']}_lazy.{$image->extension}");
        });
    }


    public function genVariants($imageId, $sizeConf)
    {

        $imageModel = Image::find($imageId);
        $imagePath = "public/{$imageModel->path}{$imageModel->title}.{$imageModel->extension}";

        $imageFile = Storage::get($imagePath);

        foreach ($this->sizeConf[$sizeConf] as $size) {
            $newImage =  $this->resize(IntImage::make($imageFile), $size);

            $filePath = 'public/' . $imageModel->path . $imageModel->title . $this->sizes[$size]['slug'] . '.' . $imageModel->extension;
            $filePathLazy = 'public/' . $imageModel->path . $imageModel->title . $this->sizes[$size]['slug'] . '_lazy.' . $imageModel->extension;

            Storage::put($filePath, $newImage->encode());
            Storage::put($filePathLazy, $this->generateLazy($newImage)->encode());
        }
        return true;
    }
}
