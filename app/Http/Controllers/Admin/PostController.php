<?php

namespace App\Http\Controllers\Admin;

use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

use App\Http\Controllers\Admin\ImageController;
use App\Http\Requests\Admin\Post\BlogPostUpdate;
use App\Http\Requests\Admin\Post\BlogPostStore;
use App\Http\Requests\Admin\Post\BlogPostUploadImage;
use App\Jobs\GenerateImageVersions;
use Validator;
use Illuminate\Support\Arr;

use App\Models\Post;
use App\Story;
use App\Image;

use function Psy\debug;

class PostController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBlogPost  $request
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $story = Story::find($id);
        $posts = $story->posts();

        return view('blog.story.show', array(
            'story' => $story,
            'posts' => $posts->get()
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::with('images')->where('id', $id)->first();
        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBlogPost  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlogPostUpdate $request, $id)
    {

        $validated = $request->validated();
        $post = Post::find($id);

        if ($post->type === 'video' && sizeof($validated) > 1) {

            $post->fill(Arr::except($validated, ['content']));

            $content = json_decode($validated['content']);

            $path =  'stories/' . $post->story_id . '/posts/' . $post->id;
            Storage::move('public/temp/' . $content->name, 'public/' . $path . '/' . $content->name);

            //delete old video from post folder.
            $old = json_decode($post->content);
            Storage::delete('public/' . $old->path . '/' . $old->name);

            $content->path = $path;
            $post->content = json_encode($content);
            $post->save();
        } else {
            $post->update($validated);
        }

        return response()->json($post, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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

    public function uploadImage(BlogPostUploadImage $request, $id)
    {

        $file = $request->file('file');

        $post = Post::find($id);
        $sizeConf = "image_post";
        $path = "{$post->story['path']}{$post->id}/";

        $imageController = new ImageController();
        $image = $imageController->save($file, $path, $sizeConf);


        $lastImage = $post->images->sortByDesc('position')->first();


        $post->images()->attach($image->id, ['position' => $lastImage ? $lastImage->position + 1 : 0]);

        return response()->json($image, 200);
    }

    public function uploadVideo(Request $request, $id)
    {

        $post = Post::find($id);
        $file = $request->file('file');

        $path = Storage::disk('local')->path("chunks/{$file->getClientOriginalName()}");

        File::append($path, $file->get());

        if ($request->has('is_last') && $request->boolean('is_last')) {
            $name = basename($path, '.part');
            $publicPath = "{$post->story['path']}{$post->id}";
            $storagePath = 'public/' . $publicPath;

            if (!Storage::exists($storagePath)) {
                Storage::makeDirectory($storagePath);
            }


            File::move($path,  Storage::disk('local')->path($storagePath . '/' . $name));

            $video = array(
                'path' => 'storage/' . $publicPath,
                'filename' => $name
            );
            $post->content = json_encode($video);
            $post->save();

            return response()->json($post);
        }


        return response()->json(['uploaded' => true]);
    }

    public function deleteVideo(Request $request, $id)
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


    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace("." . $extension, "", $file->getClientOriginalName()); // Filename without extension

        // Add timestamp hash to name of the file
        $filename .= "_" . md5(time()) . "." . $extension;

        return $filename;
    }

    public function deleteImage($id)
    {
        $image = Image::find($id);
        $dir = scandir(public_path('storage/' . $image->path));
        foreach ($dir as $scanImage) {
            if (strpos($scanImage, $image->title) === 0) {
                unlink(public_path('storage/' . $image->path . '/' . $scanImage));
            }
        }
        if ($image->delete()) {
            return response()->json(['success'   => 'true']);
        }
    }


    public function changeImagePosition(Request $request, $id)
    {

        $album = Post::find($id);
        $album->images()->updateExistingPivot($request->image_id, ['position' => $request->position]);
        return response()->json(['success' => true]);
    }

    //regenerate Image Sizes from Image posts
    public function generateImages($id)
    {
        $post = Post::find($id);

        if ($post->type != 'image') {
            return response()->json([
                'message' => 'kein Bilder Beitrag'
            ]);
        }
        /*         $imageController = new ImageController();
        foreach ($post->images as $image) {
            $message[] = $imageController->generate($image->path . $image->title . '.' . $image->extension, $image->path, $sizeConf);
        };
 */
        foreach ($post->images as $image) {
            GenerateImageVersions::dispatch($image, 'string', 'image_post');
            $message[] = 'ein Bild (ID: ' . $image->id . ') wurde zur Warteschlange hinzugefügt';
        };

        return response()->json([
            'message' => $message
        ]);
    }
}
