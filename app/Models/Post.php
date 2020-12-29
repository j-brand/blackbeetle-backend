<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'story_id', 'title', 'content', 'position', 'type', 'active', 'date'
    ];

    protected $guarded = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function images()
    {
        return $this->belongsToMany('App\Image')->withPivot('position')->orderBy('pivot_position', 'asc');
    }

    public function story()
    {
        return $this->belongsTo('App\Story');
    }
}
