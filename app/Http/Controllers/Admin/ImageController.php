<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Image\ImageUpdate;

use App\Http\Traits\ImageTrait;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use IntImage;

use App\Models\Image;

class ImageController extends Controller
{
    use ImageTrait;


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /*         $image = Storage::get('uploads/images/' . $album_id . '/' . $image_name);
        return response()->make($image, 200, ['content-type' => 'image']); */
    }


    public function getImage($album_id, $image_name)
    {
        $path = storage_path('app/uploads/albums/' . $album_id . '/' . $image_name);

        if (File::exists($path)) {
            return Image::make($path)->response();
        }
    }

    public function update(ImageUpdate $request, $id)
    {

        $validated = $request->validated();

        $image = Image::find($id);
        $image->update($validated);

        return response()->json($image, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->deleteImageFile($id);
        $image = Image::find($id)->delete();
        return true;
    }


    /* 
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
    } */
}
