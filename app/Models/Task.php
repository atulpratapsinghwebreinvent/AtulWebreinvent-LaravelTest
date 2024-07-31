<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'slug', 'description'];

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

