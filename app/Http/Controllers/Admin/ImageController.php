<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Admin\Image\ImageUpdate;

use App\Http\Traits\ImageTrait;

use Storage;

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
        $image = Storage::get('uploads/images/' . $album_id . '/' . $image_name);
        return response()->make($image, 200, ['content-type' => 'image']);
    }


    public function getImage($album_id, $image_name)
    {
        $path = storage_path('app/uploads/albums/' . $album_id . '/' . $image_name);

        if (\File::exists($path)) {
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

}
