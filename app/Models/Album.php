<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Image;


class Album extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title_image',
        'title_image_text',
        'title',
        'description',
        'active',
        'slug',
        'start_date',
        'end_date',
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

    public function images()
    {
        return $this->belongsToMany('App\Models\Image')->withPivot('position')->orderBy('pivot_position', 'asc');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }

    public function title_image()
    {
        return $this->hasOne('App\Models\Image', 'id', 'title_image');
    }

    public function titleImage()
    {
        $titleImage = Image::where('id', $this->title_image)->get();

        $imagePath = $titleImage[0]->path . $titleImage[0]->title . '_athn.' . $titleImage[0]->extension;
        return $imagePath;
    }

}
