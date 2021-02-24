<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Admin\UpdateOption;

use App\Mail\NewStoryPost;

use App\Models\Story;
use App\Models\Album;
use App\Models\Post;

use DB;
use Mail;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function getDashboard()
    {
        $data = array(
            'stories'   => Story::orderBy('created_at', 'DESC')->withCount('posts')->get(),
            'albums'    => Album::orderBy('created_at', 'DESC')->withCount('images')->get(),
        );

        return response()->json($data);
    }


/*     public function mailTheCrowd($id)
    {
        $story = Story::find($id);
        $lastpost = Post::where('story_id', $story->id)->latest('position')->first();
        $subscribers = DB::table('newsletter_subscriptions')->select('email', 'email_token')->where([
            ['story_id', '=', $id],
            ['verified', '=', 1]
        ])->get();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->queue(new NewStoryPost($story, $subscriber, $lastpost->title));
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
 */

    public function getOption($optionName)
    {
        $option = DB::table('options')->select('option', 'content')
            ->where('option', $optionName)
            ->first();
        return response()->json($option);
    }

    public function updateOption(UpdateOption $request)
    {

        $validated = $request->validated();

        $option = tap(DB::table('options')->where('option', $validated['option']))
            ->update(['content' => $validated['content']])
            ->first();

        return response()->json($option);
    }
}
