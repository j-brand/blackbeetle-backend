<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CommentStore;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentStore $request)
    {
        $validated = $request->validated();

        $comment = new Comment();
        $comment->post_id = $validated['post_id'];
        $comment->user_id = 69;
        $comment->name = $validated['name'];
        $comment->content = $validated['content'];
        $comment->save();

        return response()->json($comment, 200);
    }
}
