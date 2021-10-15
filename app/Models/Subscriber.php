<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'name', 'email'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
