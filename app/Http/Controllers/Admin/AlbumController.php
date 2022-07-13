<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;

use App\Http\Traits\ImageTrait;

use App\Http\Requests\Album\AlbumChangeImagePosition;
use App\Http\Requests\Album\AlbumStore;
use App\Http\Requests\Album\AlbumUpdate;
use App\Http\Requests\Album\AlbumUploadImage;
use App\Jobs\GenerateImageVersions;
use App\Models\Album;

class AlbumController extends Controller
{
    use ImageTrait;

    /**
     * Retruns all albums with images count
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $albums = Album::withCount('images')->get();
        return response()->json($albums);
    }

    /**
     * Stores a album in the database
     * 
     * @param App\Http\Request\Album\AlbumStore $request
     * 
     * @return json return the created album resource
     */
    public function store(AlbumStore $request)
    {

        $validated = $request->validated();

        $album = new album();
        $album->fill(Arr::except($validated, ['image_upload']));
        $album->slug = Str::slug($validated['title']);
        $album->save();

        $album->path = 'albums/' . $album->id . '/';

        //create & save cover image and al variants
        $newImage = $this->saveImage($validated['image_upload'], $album->path);
        $this->genVariants($newImage->id, 'album_title_image');

        $album->title_image = $newImage->id;
        $album->save();

        return response()->json($album);
    }

    /**
     * Searches for a album (by ID) and returns it with all images
     * 
     * @param int $id   -   Album ID
     * 
     * @return json return the album resource
     */
    public function get($id)
    {
        $album = Album::with(['images', 'title_image'])->where('id', $id)->first();
        return response()->json($album);
    }

    /**
     * Update the album resource and create/save new title image if needed
     * 
     * @param App\Http\Request\Album\AlbumUpdate $request
     * @param int $id   -   Album ID
     * 
     * @return json return the updated album resource
     */
    public function update(AlbumUpdate $request, $id)
    {

        $validated = $request->validated();

        $album = Album::find($id);

        if (Arr::exists($validated, 'image_upload')) {
            $newImage = $this->saveImage($validated['image_upload'], $album->path);
            $this->genVariants($newImage->id, 'album_title_image');

            $validated = Arr::add($validated, 'title_image', $newImage->id);
            $this->deleteImageFile($album->title_image);
            $album->title_image = $newImage->id;
        }
        $album->update(Arr::except($validated, 'image_upload'));

        $album = Album::with(['images', 'title_image'])->where('id', $id)->first();
        return response()->json($album, 200);
    }

    /**
     * Remove the album nad all image resources from storage & database.
     *
     * @param  int  $id
     * @return boolean
     */
    public function destroy($id)
    {
        $album = Album::find($id);
        $album->images()->delete();
        $albumPath = $album->path;

        if ($album->delete()) {
            Storage::deleteDirectory('public/' . $albumPath);
            if (is_dir(public_path($albumPath))) {
                $folder_delete = rmdir(public_path($albumPath));
            }
        }
        return response()->json(true);
    }

    /**
     * Upload/Save image to given album
     * 
     * @param App\Http\Request\Album\AlbumUploadImage $request
     * @param int $id   -   Album ID
     * 
     * @return json - uploaded image resource
     */
    public function upload(AlbumUploadImage $request, $id)
    {
        $validated = $request->validated();


        $album = Album::find($id);
        $sizeConf = "album_image";

        $image = $this->saveImage($validated['file'], $album->path);
        $this->genVariants($image->id, $sizeConf);

        $imageCount = $album->images->count();
        $album->images()->attach($image->id, ['position' => $imageCount + 1]);

        return response()->json($image, 200);
    }


    /**
     * Change the position of an image
     * 
     * @param App\Http\Request\Album\AlbumChangeImagePosition $request
     * @param int $id   -   Album ID
     * 
     * @return boolean
     */
    public function changeImagePosition(AlbumChangeImagePosition $request, $id)
    {
        $validated = $request->validated();

        $album = Album::find($id);
        $album->images()->updateExistingPivot($validated['image_id'], ['position' => $validated['position']]);
        return response()->json(['success' => true]);
    }

    public function regenerateImages()
    {

        $albums = Album::with('images')->get()->pluck('images')->collapse()->pluck('id');

        $albums->map(function ($item) {
            GenerateImageVersions::dispatch($item, 'album_image');
        });

        return response()->json([
            'message' => 'Generation jobs queued!'
        ]);
    }

    public function regenerateTitleImages()
    {
        $albums = Album::all()->pluck('title_image');

        $albums->map(function ($item) {
            GenerateImageVersions::dispatch($item, 'album_title_image');
        });

        return response()->json([
            'message' => 'Generation jobs queued!'
        ]);
    }
}
