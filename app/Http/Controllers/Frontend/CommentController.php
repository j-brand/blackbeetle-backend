<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CommentStore;
use App\Mail\newPostComment;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;


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

        //send Email Notification to all admin Emails (.env)
        $story = $comment->post->story;
        $admin_mails = array_map('trim', explode(',', env('ADMIN_EMAIL')));
        Mail::to($admin_mails)->send(new newPostComment($story, "Neuer Kommentar"));

        return response()->json($comment, 200);
    }
}
