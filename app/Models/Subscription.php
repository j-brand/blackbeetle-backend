<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'option',
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function verified_subscriber()
    {
        return $this->subscriber()->whereNotNull('verified_at');
    }
}