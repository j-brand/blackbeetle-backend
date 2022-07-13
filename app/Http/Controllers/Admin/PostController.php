<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

use App\Http\Controllers\Controller;

use App\Models\Post;

use App\Http\Traits\ImageTrait;

use App\Http\Requests\Post\BlogPostUpdate;
use App\Http\Requests\Post\BlogPostStore;
use App\Http\Requests\Post\BlogPostUploadImage;
use App\Http\Requests\Post\BlogPostChangeImagePosition;
use App\Http\Requests\Post\BlogPostUploadVideo;

class PostController extends Controller
{

    use ImageTrait;

    /**
     * Store a newly created post resource in storage.
     *
     * @param  \App\Http\Requests\StoreBlogPost  $request
     * @return \Illuminate\Http\Response json new created blogpost resource
     */
    public function store(BlogPostStore $request)
    {
        $validated = $request->validated();

        $postsCount = Post::where('story_id', $validated['story_id'])->count();

        $post = new Post();
        $post->fill($validated);
        $post->user_id = 1;
        $post->position = $postsCount + 1;
        $post->date = date('Y-m-d');
        $post->save();

        return response()->json($post, 200);
    }



    /**
     * Searches for a post (by ID) and returns it with all images
     * 
     * @param int $id   -   Post ID
     * 
     * @return json return the post resource
     */
    public function get($id)
    {
        $post = Post::with('images')->where('id', $id)->first();
        return response()->json($post, 200);
    }

    /**
     * Update the post resource and create/save new title image if needed
     * 
     * @param App\Http\Request\Post\BlogPostUpdate $request
     * @param int $id   -   post ID
     * 
     * @return json return the updated post resource
     */
    public function update(BlogPostUpdate $request, $id)
    {

        $validated = $request->validated();

        $post = Post::find($id);
        $post->update($validated);

        return response()->json($post, 200);
    }

    /**
     * Remove the specified post resource from storage & database.
     *
     * @param  int  $id
     * @return boolean 
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        $postFolder = "public/{$post->story['path']}{$post->id}";

        if (Storage::exists($postFolder)) {
            Storage::deleteDirectory($postFolder);
        }

        $post->delete();
        return response()->json(true);
    }

    /**
     * Upload/save image to given post
     * 
     * @param App\Http\Request\Post\BlogPostUploadImage $request
     * @param int $id   -   post ID
     * 
     * @return json return the uploaded image resource
     */
    public function uploadImage(BlogPostUploadImage $request, $id)
    {

        $validated = $request->validated();

        $post = Post::find($id);
        $path = "{$post->story['path']}{$post->id}/";

        $newImage = $this->saveImage($validated['file'], $path);
        $this->genVariants($newImage->id, "image_post");

        $lastImage = $post->images->sortByDesc('position')->first();
        $post->images()->attach($newImage->id, ['position' => $lastImage ? $lastImage->position + 1 : 0]);

        return response()->json($newImage, 200);
    }

    /**
     * Upload/save video chunks and put the video together if last chunk is sent
     * 
     * @param App\Http\Request\Post\BlogPostUploadVideo $request
     * @param int $id   -   post ID
     * 
     * @return json if is last chunk return the video resource if not return boolean (true)
     */
    public function uploadVideo(BlogPostUploadVideo $request, $id)
    {

        $validated = $request->validated();
        $post = Post::find($id);

        $file = $validated['file'];

        $path = Storage::disk('local')->path("chunks/{$file->getClientOriginalName()}");

        File::append($path, $file->get());

        if (Arr::has($validated, 'is_last') && $validated['is_last'] == '1') {

            $name = basename($path, '.part');
            $publicPath = "{$post->story['path']}{$post->id}";
            $storagePath = 'public/' . $publicPath;

            if (!Storage::exists($storagePath)) {
                Storage::makeDirectory($storagePath);
            }

            File::move($path,  Storage::disk('local')->path($storagePath . '/' . $name));

            $video = array(
                'path' => 'storage/' . $publicPath,
                'filename' => $name,
            );
            $post->content = json_encode($video);
            $post->save();

            return response()->json($post);
        }
        return response()->json(['uploaded' => true]);
    }

    /**
     * Delete the video file from a given post
     * 
     * @param int $id   -   post ID
     * 
     * @return json return json error if not found
     */
    public function deleteVideo($id)
    {
        $post = Post::find($id);
        $postContent = json_decode($post->content);

        $filePath = $postContent->path . '/' . $postContent->filename;
        if (File::exists($filePath)) {
            File::delete($filePath);
            $post->content = '';
            $post->save();
            return response()->json(true);
        }
        return response()->json(['message' => 'file not found', 'path' => $filePath]);
    }

    /**
     * Change the position of an image
     * 
     * @param App\Http\Request\Post\PostChangeImagePosition $request
     * @param int $id   -   Post ID
     * 
     * @return boolean
     */
    public function changeImagePosition(BlogPostChangeImagePosition $request, $id)
    {

        $validated = $request->validated();

        $album = Post::find($id);
        $album->images()->updateExistingPivot($validated['image_id'], ['position' => $validated['position']]);
        return response()->json(['success' => true]);
    }
    /* 
    //regenerate Image Sizes from Image posts
    public function generateImages($id)
    {
        $post = Post::find($id);

        if ($post->type != 'image') {
            return response()->json([
                'message' => 'kein Bilder Beitrag'
            ]);
        }

        foreach ($post->images as $image) {
            GenerateImageVersions::dispatch($image, 'string', 'image_post');
            $message[] = 'ein Bild (ID: ' . $image->id . ') wurde zur Warteschlange hinzugefügt';
        };

        return response()->json([
            'message' => $message
        ]);
    } */
}
