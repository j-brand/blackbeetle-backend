<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{

    use HasFactory;
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
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function posts()
    {
        return $this->hasMany('App\Models\Post');
    }

    public function title_image()
    {
        return $this->hasOne('App\Models\Image', 'id', 'title_image');
    }

    public function images()
    {
        return $this->belongsToMany('App\Models\Image');
    }
}
