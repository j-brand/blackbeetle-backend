<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Admin\ImageController;
use Illuminate\Http\Request;
use Validator;
use Log;

use App\Story;
use App\Image;
use Str;

class StoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stories = Story::withCount('posts')->get();
        return view('admin.story.index', compact('stories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.story.create');
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
            'slug'                => 'unique:albums|string|max:255',
            'title'               => 'required|unique:albums|string|max:255',
            'description'         => 'string|max:1000',
            'image_upload'        => 'required|mimes:jpeg,bmp,png,jpg,JPG|max:2500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $story = new story();
        $story->fill($request->except(['image_upload']));
        if (!$request->slug) {
            $story->slug = Str::slug($request->get('title'));
        }
        $story->save();

        $story->path = 'stories/' . $story->id . '/';
        $sizeConf="story_title_image";

        if ($request->hasFile('image_upload')) {
            $file = $request->file('image_upload');

            $imageController = new ImageController();
            $response = $imageController->save($file, $story->path, $sizeConf);
            $story->title_image = $response->id;
        } else {
            $story->title_image = 1;
        }

        $story->save();

        return response()->json([
            'success' => 'true',
            'story_id' => $story->id
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $story = Story::find($id);
        $title_image = $story->title_image()->first();
        $posts = $story->posts()->orderBy('position', 'asc')->get();

        return view('admin.story.edit', compact('story', 'title_image', 'posts'));
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
            'title'               => 'unique:albums|string|max:255',
            'slug'                => 'unique:albums|string|max:255',
            'description'         => 'max:1000',
            'image_upload'        => 'mimes:jpeg,bmp,png,jpg,JPG',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }


        $story = Story::find($id);
        $sizeConf = "story_title_image";


        if ($request->hasFile('image_upload')) {
            $file = $request->file('image_upload');

            $imageController = new ImageController();
            $response = $imageController->save($file, $story->path, $sizeConf);
            $request->merge(['title_image' => $response->id]);


            $story->fill($request->except(['image_upload']))->save();
            return response()->json(['success' => true, 'imgData' => $response]);
        } else {
            $story->fill($request->except(['image_upload']))->save();
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
        $story = Story::find($id);
        $storyPath = $story->path;

        if ($story->delete()) {
            Storage::deleteDirectory('public/' . $storyPath);
        }
        return response()->json(['success' => true]);
    }


}
