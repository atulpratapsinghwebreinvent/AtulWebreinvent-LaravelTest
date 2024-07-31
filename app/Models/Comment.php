<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['content', 'post_id','task_id'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function task()
    {
        //Test the Gitkraken
        return $this->belongsTo(Task::class);
    }
}

