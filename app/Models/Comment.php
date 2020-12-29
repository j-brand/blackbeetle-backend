<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id','name', 'content',
    ];

    protected $guarded = [
        'user_id',
    ];

    public function post(){
        return $this->belongsTo('App\Post');
    }
}
