<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Album;
use App\Models\Image;

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.album.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'active'              => 'integer',
            'title'               => 'required|unique:albums|string|max:255',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date',
            'description'         => 'string|max:1000',
            'title_image_text'    => 'max:255',
            'image_upload'        => 'required|mimes:jpeg,bmp,png,jpg,JPG|max:2500',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $album = new album();
        $album->fill($request->except(['image_upload']));
        $album->slug = Str::slug($request->get('title'));
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
        $title_image = Image::find($album->title_image)->first();
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
        $validator = Validator::make($request->all(), [
            'active'              => 'integer',
            'title'               => 'string|max:255',
            'start_date'          => 'date|date_format:Y-m-d',
            'end_date'            => 'date|date_format:Y-m-d',
            'description'         => 'max:1000',
            'title_image_text'    => 'max:255',
            'image_upload'        => 'mimes:jpeg,bmp,png,jpg,JPG',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $album = Album::find($id);
        $sizeConf = 'album_title_image';


        if ($request->hasFile('image_upload')) {
            $file = $request->file('image_upload');

            $imageController = new ImageController();
            $response = $imageController->save($file, $album->path, $sizeConf);
            $request->merge(['title_image' => $response->id]);


            $album->fill($request->except(['image_upload']))->save();
            return response()->json(['success' => true, 'imgData' => $response]);
        } else {
            $album->fill($request->except(['image_upload']))->save();
        }

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
        $album = Album::find($id);
        $images = Album::find($id)->images()->get();
        $albumPath = $album->path;

        if ($album->delete()) {
            Storage::deleteDirectory('public/' . $albumPath);
        }
        if (is_dir(public_path($albumPath))) {
            $folder_delete = rmdir(public_path($albumPath));
        }
        return response()->json(['success' => true]);
    }

    public function upload(Request $request, $id)
    {
        request()->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        $file = $request->file('file');

        $album = Album::find($id);
        $sizeConf = "album_image";

        $imageController = new ImageController();
        $image = $imageController->save($file, $album->path, $sizeConf);

        $lastImage = $album->images->sortByDesc('position')->first();

        $album->images()->attach($image->id, ['position' => $lastImage ? $lastImage->position + 1 : 0]);

        return response()->json($image, 200);
    }

    public function changeImagePosition(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'image_one'            => 'required|integer',
            'image_two'            => 'required|integer',
            'posi_one'            => 'required|integer',
            'posi_two'            => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $album = Album::find($id);
        $album->images()->updateExistingPivot($request->image_one, ['position' => $request->posi_one]);
        $album->images()->updateExistingPivot($request->image_two, ['position' => $request->posi_two]);

        return response()->json(['success' => true]);
    }

    public function deleteImage($id)
    {
        $imageController = new ImageController();
        $sizeConf = "album_image";

        $imageController->destroy($id, $sizeConf);

        return response()->json([
            'success' => 'true'
        ]);
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
