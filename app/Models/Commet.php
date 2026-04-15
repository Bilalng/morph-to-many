<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Commet extends Model
{
    protected $fillable = [
        'content',
        'commantable_type',
        'commantable_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commantable()
    {
        return $this->morphTo();
    }

    public function replies(){
        return $this->belongsToMany(
            Comment::class,
            'comment_replies',
            'comment_id',
            'reply_id'
        );
    }

    public function parent(){
        return $this->belongsToMany(
            Comment::class,
            'comment_replies',
            'reply_id',
            'comment_id'
        );
    }
}
