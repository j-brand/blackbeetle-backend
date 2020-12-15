<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'title', 'description', 'path', 'extension', 'filesize', 'height', 'width'
    ];

    public function stories()
    {
        return $this->belongsToMany('App\Models\Story');
    }

    public function albums()
    {
        return $this->belongsToMany('App\Models\Album');
    }

    public function posts()
    {
        return $this->belongsToMany('App\Models\Post');
    }

    public function getPath()
    {
        return 'storage/' . $this->path . $this->title . '.' . $this->extension;
    }
}
