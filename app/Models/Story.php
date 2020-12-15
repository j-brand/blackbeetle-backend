<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $table = 'stories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title_image',
        'title',
        'description',
        'active',
        'slug',
    ];

	public function posts(){
        return $this->hasMany('App\Post');
    }
  
    public function title_image(){
        return $this->hasOne('App\Image','id', 'title_image');
    }

    public function images(){
        return $this->belongsToMany('App\Image');
    }
    
}
