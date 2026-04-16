<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
class Comment extends Model
{
    use HasFactory;
    protected $table = 'comments';
    protected $fillable = [
        'content',
        'commentable_type',
        'commentable_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function replies(){
        return $this->belongsToMany(
            Comment::class,
            'comment_replies',
            'parent_id',
            'reply_id'
        );
    }

    public function parent(){
        return $this->belongsToMany(
            Comment::class,
            'comment_replies',
            'reply_id',
            'parent_id'
        );
    }
}
