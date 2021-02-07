<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewStoryPost;
use Illuminate\Http\Request;

use App\User;
use App\Story;
use App\Album;
use App\Http\Requests\Admin\GetOption;
use App\Http\Requests\Admin\UpdateOption;
use App\Post;

use DB;
use Mail;
use Validator;
use File;

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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $users          = User::all();
        $stories        = Story::orderBy('created_at', 'DESC')->get();
        $postCount      = Post::all()->count();
        $imagesCount    = 0;
        $albums         = Album::all();
        $c              = DB::table('options')->select('option')->where('id', 1)->get();
        $coords = explode('|', $c[0]->option);

        foreach ($albums as $album) {
            $imagesCount =  $imagesCount + $album->images()->count();
        }
        return view('admin.index', compact('users', 'stories', 'albums', 'imagesCount', 'postCount', 'coords'));
    }


    public function playground()
    {
        return view('admin.playground');
    }
    public function masonry()
    {
        return view('admin.playground.masonry');
    }
    public function phpinfo()
    {
        return view('admin.playground.phpinfo');
    }
    public function hofmann()
    {
        return view('admin.playground.hofmann');
    }
    public function anim()
    {
        return view('admin.playground.anim');
    }

    public function mailTheCrowd($id)
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

    public function setCoords(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coords'        => 'required|string',
            'bounds'        => 'required|string',
            'description'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $input = $request->all();
        if (!$input['description']) {
            $input['description'] = 'Joh & Isa';
        }

        $coords = array($input['coords'], $input['description'], $input['bounds']);

        $json_array = json_encode($coords);
        $result = DB::table('options')
            ->where('id', 1)
            ->update(['option' => implode('|', $coords)]);

        if ($result == 1) {
            return response()->json([
                'success' => true,
            ], 200);
        }
    }

    public function getOption($optionName)
    {
        $option = DB::table('options')->select('option', 'content')
            ->where('option', $optionName)
            ->first();
        return response()->json($option);
    }

    public function updateOption(UpdateOption $request)
    {
        $option = tap(DB::table('options')->where('option', $request->option))
            ->update(['content' => $request->content])
            ->first();

        return response()->json($option);
    }
}
