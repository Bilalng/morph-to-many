<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
