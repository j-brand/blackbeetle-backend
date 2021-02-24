<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Storage;
use Str;
use Arr;

use App\Http\Traits\ImageTrait;

use App\Http\Requests\Admin\Story\StoryStore;
use App\Http\Requests\Admin\Story\StoryUpdate;
use App\Models\Story;
use App\Models\Image;

class StoryController extends Controller
{

    use ImageTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stories = Story::withCount('posts')->get();
        return response()->json($stories);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoryStore $request)
    {

        $validated = $request->validated();

        $story = new story();
        $story->fill(Arr::except($validated, ['image_upload']));
        $story->slug = Str::slug($validated['title']);
        $story->title_image = 1;
        $story->save();

        $story->path = 'stories/' . $story->id . '/';
        $sizeConf = "story_title_image";
        $newImage = $this->saveImage($validated['image_upload'], $story->path, $sizeConf);

        $story->title_image = $newImage->id;
        $story->save();

        return response()->json($story);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $story = Story::with(['posts', 'title_image'])->where('id', $id)->first();
        return response()->json($story);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoryUpdate $request, $id)
    {
        $validated = $request->validated();

        $story = Story::find($id);

        if (Arr::exists($validated, 'image_upload')) {
            $sizeConf = "story_title_image";
            $newImage = $this->saveImage($validated['image_upload'], $story->path, $sizeConf);
            $validated = Arr::add($validated, 'title_image', $newImage->id);
            $this->deleteImageFile($story->title_image);
        }

        $story->update(Arr::except($validated, 'image_upload'));

        $story = Story::with(['posts', 'title_image'])->where('id', $id)->first();
        return response()->json($story);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $story = Story::find($id);
        $storyPath = $story->path;

        if ($story->delete()) {
            Storage::deleteDirectory('public/' . $storyPath);
        }
        return response()->json(['success' => true]);
    }
}
