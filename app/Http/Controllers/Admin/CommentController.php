<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CommentUpdate;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getByPost($id)
    {

        $comments = Post::find($id)->comments()->get();

        return response()->json($comments, 200);
    }


    /**
     * Searches for a comment (by ID) and returns it
     * 
     * @param int $id   -   Comment ID
     * 
     * @return json return the post resource
     */
    public function get($id)
    {
        $comment = Comment::find($id);
        return response()->json($comment, 200);
    }


    /**
     * Update the comment resource
     * 
     * @param App\Http\Request\Comment\CommentUpdate $request
     * @param int $id   -   comment ID
     * 
     * @return json return the updated comment resource
     */
    public function update(CommentUpdate $request, $id)
    {

        $validated = $request->validated();

        $comment = Comment::find($id);
        $comment->update($validated);

        return response()->json($comment, 200);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::find($id);
        $comment->delete();
        return response()->json(['success' => true]);
    }
}
