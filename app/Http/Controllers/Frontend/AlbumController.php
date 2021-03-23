<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\Models\Album;

class AlbumController extends Controller
{
    public function getAlbums()
    {
        $albums = Album::with('title_image')->where('active', 1)->withCount('images')->get();
        return response()->json($albums);
    }

    public function getAlbumBySlug($slug)
    {
        $album = Album::with(['images', 'title_image'])->where('slug', $slug)->where('active', 1)->first();
        return response()->json($album);
    }
}
