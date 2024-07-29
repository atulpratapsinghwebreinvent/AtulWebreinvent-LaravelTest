<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'content'];
    protected $table = 'posts';

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
