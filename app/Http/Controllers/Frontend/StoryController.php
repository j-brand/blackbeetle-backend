<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\Models\Story;

class StoryController extends Controller
{
    public function getStories()
    {
        $stories = Story::with('title_image')->orderBy('position', 'desc')->where('active', 1)->withCount('posts')->get();
        return response()->json($stories);
    }

    public function getStoryBySlug($slug, $order)
    {

        $story = Story::with(['title_image'])->where('slug', $slug)->where('active', 1)->first();
        $posts = $story->posts()->where('active', 1)->with('images','comments')->orderBy('position', $order)->paginate(5);
        $story->posts = $posts;

        return response()->json($story);
    }
}
