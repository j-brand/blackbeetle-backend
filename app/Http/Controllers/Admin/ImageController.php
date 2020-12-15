<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
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
        7   =>  array('width' => 1920,   'height' => 1080,   'slug' => '_hd',      'method' => 'fit'),
        /* FACEBOOK SHARE IMAGE */
        8   =>  array('width' => 1200,   'height' => 630,    'slug' => '_fb',      'method' => 'fit'),
        9   =>  array('width' => 'null', 'height' => 50,     'slug' => '_thumb',   'method' => false),
        /* LAZY LOAD BLUR IMAGE */
        10 =>   array('width' => 60,     'height' => 'null', 'slug' => '_lazy',    'method' => false),
        11 =>   array('width' => 800,    'height' => 'null', 'slug' => '_aswipe',  'method' => false),
    );

    private $sizeConf = array(
        'album_image'       => [0, 1, 2, 9],
        'album_title_image' => [0, 2, 3, 4, 5, 6, 7, 9],
        'story_title_image' => [0, 2, 3, 4, 8],
        'image_post'        => [0, 1, 2, 9, 11],
    );

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $image = Storage::get('uploads/images/' . $album_id . '/' . $image_name);
        return response()->make($image, 200, ['content-type' => 'image']);
    }


    public function getImage($album_id, $image_name)
    {
        $path = storage_path('app/uploads/albums/' . $album_id . '/' . $image_name);

        if (\File::exists($path)) {
            return \Image::make($path)->response();
        }
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'description'         => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $image = Image::find($id);

        $image->fill($request->all())->save();

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = Image::find($id);

        foreach ($this->sizes as $variant) {
            if (Storage::exists('public/' . $image->path . $image->title . $variant['slug'] . '.' . $image->extension)) {
                Storage::delete('public/' . $image->path . $image->title . $variant['slug'] . '.' . $image->extension);
            }
            if (Storage::exists('public/' . $image->path . $image->title . $variant['slug'] . '_lazy.' . $image->extension)) {
                Storage::delete('public/' . $image->path . $image->title . $variant['slug'] . '_lazy.' . $image->extension);
            }
        }

        $image->delete();

        return true;
    }

    private function resize($image, $size)
    {

        if (array_key_exists($size, $this->sizes)) {

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
        } else {
            return false;
        }
    }

    public function save($image, $path, $sizeConf)
    {
        $file   = $image->getClientOriginalName();
        $filename = uniqid('img_');
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        $imageFile = IntImage::make($image);

        if (Storage::disk('local')->exists('public/' . $path . $filename . '.' . $extension)) {
            $filename .= '_01';
        }
        foreach ($this->sizeConf[$sizeConf] as $size) {
            $newImage =  $this->resize(IntImage::make($image), $size);

            $filePath = 'public/' . $path . $filename . $this->sizes[$size]['slug'] . '.' . $extension;
            $filePathLazy = 'public/' . $path . $filename . $this->sizes[$size]['slug'] . '_lazy.' . $extension;

            if ($newImage) {
                Storage::put($filePath, $newImage->encode());
                Storage::put($filePathLazy, $this->generateLazy($newImage)->encode());
            } else {
                return false;
            }
        }

        $imageObj               = new Image();
        $imageObj->title        = $filename;
        $imageObj->path         = $path;
        $imageObj->width        = $imageFile->width();
        $imageObj->height       = $imageFile->height();
        $imageObj->extension    = $extension;
        $imageObj->created_at   = Carbon::now();
        $imageObj->save();

        return $imageObj;
    }


    public function generate($imgPath, $path, $sizeConf)
    {
        $imagePath = $imgPath;

        if (strpos($imagePath, "storage/") === 0) {
            $imagePath = substr($imagePath, 8);
        }

        if (!Storage::disk('local')->exists('public/' . $imagePath)) {
            return 'Datei nicht gefunden: ' . 'public/' . $imagePath;
        }

        $fileInfo = pathinfo('public/' . $imagePath);
        $file = Storage::get('public/' . $imagePath);

        foreach ($this->sizeConf[$sizeConf] as $size) {
            $newImage =  $this->resize(IntImage::make($file), $size);

            $filePath =  $fileInfo['dirname'] . '/' . $fileInfo['filename'] . $this->sizes[$size]['slug'] . '.' . $fileInfo['extension'];
            $filePathLazy =  $fileInfo['dirname'] . '/' . $fileInfo['filename'] . $this->sizes[$size]['slug'] . '_lazy.' . $fileInfo['extension'];

            if ($newImage) {
                Storage::put($filePath, $newImage->encode());
                Storage::put($filePathLazy, $this->generateLazy($newImage)->encode());
            } else {
                return false;
            }
        }
        return response()->json('images generated!');
    }

    public function generateLazy($image)
    {
        $image->resize(60, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->blur(15);
        return $image;
    }
}
