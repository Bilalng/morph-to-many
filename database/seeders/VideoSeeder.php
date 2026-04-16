<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Video;
use App\Models\User;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        // 3 video oluştur
        Video::factory(3)->make()->each(function (Video $video) use ($users) {
            $video->user_id = $users->random()->id;
            $video->save();

            // Her video'ya 2 üst düzey yorum ekle
            $parentComments = Comment::factory(2)->create([
                'commentable_type' => 'video',
                'commentable_id'   => $video->id,
            ]);

            // Her yoruma 2 yanıt ekle
            foreach ($parentComments as $parent) {
                $replies = Comment::factory(2)->create([
                    'commentable_type' => 'video',
                    'commentable_id'   => $video->id,
                ]);

                foreach ($replies as $reply) {
                    $parent->replies()->attach($reply->id);
                }
            }
        });
    }
}
