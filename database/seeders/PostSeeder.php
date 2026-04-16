<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        // 5 post oluştur
        Post::factory(5)->make()->each(function (Post $post) use ($users) {
            $post->user_id = $users->random()->id;
            $post->save();

            // Her post'a 3 üst düzey yorum ekle
            $parentComments = Comment::factory(3)->create([
                'commentable_type' => 'post',
                'commentable_id'   => $post->id,
            ]);

            // Her yoruma 2 yanıt ekle
            foreach ($parentComments as $parent) {
                $replies = Comment::factory(2)->create([
                    'commentable_type' => 'post',
                    'commentable_id'   => $post->id,
                ]);

                foreach ($replies as $reply) {
                    $parent->replies()->attach($reply->id);
                }
            }
        });
    }
}
