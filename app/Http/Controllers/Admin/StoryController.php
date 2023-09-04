<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\StoryUpdate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Http\Traits\ImageTrait;

use App\Http\Requests\Story\StoryStore;
use App\Jobs\GenerateImageVersions;
use App\Models\Story;

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
        $stories = Story::withCount('posts')->orderBy('position')->get();
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

        $newImage = $this->saveImage($validated['image_upload'], $story->path);
        $this->genVariants($newImage->id, "story_title_image");

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
            $newImage = $this->saveImage($validated['image_upload'], $story->path);
            $this->genVariants($newImage->id, "story_title_image");

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

    public function regenerateTitleImages()
    {


        $stories = Story::all()->pluck('title_image');

        $stories->map(function ($item) {
            GenerateImageVersions::dispatch($item, 'story_title_image');
        });

        return response()->json([
            'message' => 'Generation jobs queued!'
        ]);
    }
}
