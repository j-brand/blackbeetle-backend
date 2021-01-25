<?php

namespace App\Http\Controllers\Admin;

use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBlogPost;
use App\Http\Requests\UpdateBlogPost;
use Illuminate\Http\UploadedFile;

use App\Http\Controllers\Admin\ImageController;
use App\Http\Requests\Post\BlogPostUpdate;
use App\Http\Requests\Post\BlogPostStore;
use App\Http\Requests\Post\BlogPostUploadImage;
use App\Jobs\GenerateImageVersions;
use Validator;

use App\Models\Post;
use App\Story;
use App\Image;

use function Psy\debug;

class PostController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return view('admin.post.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select($storyID)
    {
        return view('admin.post.select', compact('storyID'));
    }

    public function create(Request $request, $storyID, $typ)
    {

        switch ($typ) {
            case 'html':
                return view('admin.post.create.html', compact('storyID'));
                break;
            case 'image':
                return view('admin.post.create.image', compact('storyID'));
                break;
            case 'map':
                return view('admin.post.create.map', compact('storyID'));
                break;
            case 'video':
                return view('admin.post.create.video', compact('storyID'));
                break;
            default:
                return response()->json(['success' => false]);
                break;
        }
    }

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

        if ($validated['type'] === 'video') {

            $content = json_decode($validated['content']);

            $path =  'stories/' . $validated['story_id'] . '/posts/' . $post->id;
            Storage::move('public/temp/' . $content->name, 'public/' . $path . '/' . $content->name);

            $content->path = $path;
            $post->content = json_encode($content);
            $post->save();
        }

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

            $post->fill($request->except(['content']));

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
        if (Storage::disk('local')->exists('public/stories/' . $post->story_id . '/' . $post->id)) {
            Storage::deleteDirectory('public/stories/' . $post->story_id . '/' . $post->id);
        }
        $post->delete();
        return response()->json(['success' => true, 'message' => 'Beitrag wurde gelöscht.']);
    }

    public function uploadImage(BlogPostUploadImage $request, $id)
    {

        $file = $request->file('file');

        $post = Post::find($id);
        $sizeConf = "image_post";
        $path = $post->story()->value('path') . $post->id . '/';


        $imageController = new ImageController();
        $image = $imageController->save($file, $path, $sizeConf);


        $lastImage = $post->images->sortByDesc('position')->first();


        $post->images()->attach($image->id, ['position' => $lastImage ? $lastImage->position + 1 : 0]);

        return response()->json($image, 200);
    }

    /**
     * Handles the file upload
     *
     * @param FileReceiver $receiver
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws UploadMissingFileException
     *
     */
    public function uploadVideo(FileReceiver $receiver)
    {
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }
        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            $file = $save->getFile();
            $mime = $file->getMimeType();

            // save the file and return any response you need
            $fileName = $this->createFilename($file);
            $finalPath = storage_path("app/public/temp");

            $file->move($finalPath, $fileName);


            return response()->json([
                'path' => 'temp',
                'name' => $fileName,
                'mime_type' => $mime
            ]);
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();
        return response()->json([
            "done" => $handler->getPercentageDone()
        ]);
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
