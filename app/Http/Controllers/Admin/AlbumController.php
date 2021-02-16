<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Album\AlbumChangeImagePosition;
use App\Http\Requests\Admin\Album\AlbumStore;
use App\Http\Requests\Admin\Album\AlbumUploadImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{

    /**
     * Display a listing of the resource. 
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $albums = Album::withCount('images')->get();
        return response()->json($albums);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AlbumStore $request)
    {
        $album = new album();
        $album->fill($request->except(['image_upload']));
        $album->slug = Str::slug($request->title);
        $album->save();

        $album->path = 'albums/' . $album->id . '/';

        $sizeConf = 'album_title_image';

        if ($request->hasFile('image_upload')) {
            $file = $request->file('image_upload');

            $imageController = new ImageController();
            $response = $imageController->save($file, $album->path, $sizeConf);
            $album->title_image = $response->id;
        } else {
            $album->title_image = 1;
        }
        $album->save();
        return response()->json($album);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $album = Album::with('images')->where('id', $id)->first();
        $title_image = Image::where('id', $album->title_image)->first();
        $album->title_image = $title_image;

        return response()->json($album);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $album = Album::find($id);
        $sizeConf = 'album_title_image';


        if ($request->hasFile('image_upload')) {
            $file = $request->file('image_upload');

            $imageController = new ImageController();
            $response = $imageController->save($file, $album->path, $sizeConf);
            $request->merge(['title_image' => $response->id]);
            $album->title_image = $response->id;

            $album->fill($request->except(['image_upload']))->save();
        } else {
            $album->update($request->all());
        }

        $title_image = Image::where('id', $album->title_image)->first();
        $album->title_image = $title_image;
        return response()->json($album, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $album = Album::find($id);
        $album->images()->delete();
        $albumPath = $album->path;

        if ($album->delete()) {
            Storage::deleteDirectory('public/' . $albumPath);
        }
        if (is_dir(public_path($albumPath))) {
            $folder_delete = rmdir(public_path($albumPath));
        }
        return response()->json(true);
    }

    public function upload(AlbumUploadImage $request, $id)
    {
        $file = $request->file('file');

        $album = Album::find($id);
        $sizeConf = "album_image";
        $imageController = new ImageController();
        $image = $imageController->save($file, $album->path, $sizeConf);


        $imageCount = $album->images->count();
        $album->images()->attach($image->id, ['position' => $imageCount + 1]);

        return response()->json($image, 200);
    }

    public function changeImagePosition(AlbumChangeImagePosition $request, $id)
    {
        $album = Album::find($id);
        $album->images()->updateExistingPivot($request->image_id, ['position' => $request->position]);
        return response()->json(['success' => true]);
    }

    public function deleteImage($id)
    {
        $imageController = new ImageController();
        $sizeConf = "album_image";

        $imageController->destroy($id, $sizeConf);

        return response()->json(true);
    }

    public function generate($id)
    {
        $album = Album::find($id);
        if (!$album) {
            return response()->json([
                'message' => 'Album existiert nicht.'
            ]);
        }
        $images = $album->images()->get();
        $titleImage = $album->title_image()->first();
        $message = [];
        foreach ($images as $image) {
            GenerateImageVersions::dispatch($image, $album->path, 'album_image');
            $message[] = 'ein Bild (ID: ' . $image->id . ') wurde zur Warteschlange hinzugefügt';
        };

        $titleImage === null ? GenerateImageVersions::dispatch('dummy.png', $album->path, 'album_title_image') : GenerateImageVersions::dispatch($titleImage, $album->path, 'album_title_image');

        return response()->json([
            'message' => $message
        ]);
    }
}
