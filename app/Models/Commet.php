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
}
