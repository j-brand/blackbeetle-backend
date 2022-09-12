<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Comment extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id', 'name', 'content',
    ];

    protected $guarded = [
        'user_id',
    ];
    protected $hidden = [
        'updated_at',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:d.F Y',
    ];

    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }
}
