<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    // Add the fillable attributes
    protected $fillable = ['content', 'commentable_id', 'commentable_type'];

    // Define the inverse relationship
    public function commentable()
    {
        return $this->morphTo();
    }
}
