<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;
    public function user(){
        return $this->belongsTo(User::class)->withTrashed();

    }

    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function reports(){
        return $this->morphMany(Report::class, 'reportable');
    }
}
