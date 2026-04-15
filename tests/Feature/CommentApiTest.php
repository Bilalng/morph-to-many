<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Her testten sonra veritabanını sıfırlayıp temiz bir sayfa açması için:
uses(RefreshDatabase::class);

// ---------------------------------------------------------
// 1. GET: Yorumları Listeleme Testi
// ---------------------------------------------------------
it('can fetch comments of a specific model', function () {
    // Hazırlık (Arrange): Veritabanına sahte post ve yorum bas
    $post = Post::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_type' => 'post',
        'commentable_id' => $post->id,
    ]);

    // Eylem (Act): API'ye GET isteği at
    $response = $this->getJson("/api/v1/comments?commentable_type=post&commentable_id={$post->id}");

    // İddia (Assert): 200 dönmeli ve içinde o yorum olmalı
    $response->assertStatus(200)
             ->assertJsonPath('comments.0.id', $comment->id);
});

// ---------------------------------------------------------
// 2. POST: Yeni Yorum Ekleme Testi
// ---------------------------------------------------------
it('can create a new comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    // actingAs($user) ile sisteme bu adam giriş yapmış gibi davranıyoruz
    $response = $this->actingAs($user)->postJson('/api/v1/comments', [
        'commentable_type' => 'post',
        'commentable_id' => $post->id,
        'body' => 'Pest ile test yazmak çok zevkli!'
    ]);

    $response->assertStatus(200);
    
    // Veritabanına gerçekten kaydedilmiş mi diye kontrol et
    $this->assertDatabaseHas('comments', [
        'body' => 'Pest ile test yazmak çok zevkli!',
        'user_id' => $user->id,
        'commentable_type' => 'post'
    ]);
});

// ---------------------------------------------------------
// 3. POST: Bir Yoruma Yanıt (Reply) Verme Testi (Senior Kısım)
// ---------------------------------------------------------
it('can reply to an existing comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $parentComment = Comment::factory()->create([
        'commentable_type' => 'post',
        'commentable_id' => $post->id,
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/comments', [
        'commentable_type' => 'post',
        'commentable_id' => $post->id,
        'parent_id' => $parentComment->id, // Sihirli dokunuş: Ana yorumu belirtiyoruz
        'body' => 'Kesinlikle katılıyorum kanka.'
    ]);

    $response->assertStatus(200);

    // O pivot tablo düzgün çalışıyor mu? (comment_replies tablosunu yokluyoruz)
    $this->assertDatabaseHas('comment_replies', [
        'parent_id' => $parentComment->id,
        'reply_id' => $response->json('comment.id')
    ]);
});

// ---------------------------------------------------------
// 4. DELETE: Güvenlik Testi (Başkasının yorumunu silemez)
// ---------------------------------------------------------
it('forbids users from deleting someone elses comment', function () {
    $hacker = User::factory()->create();
    $owner = User::factory()->create();
    
    $comment = Comment::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($hacker)->deleteJson("/api/v1/comments/{$comment->id}");

    $response->assertStatus(403);
    
    $this->assertDatabaseHas('comments', ['id' => $comment->id]); 
});

// ---------------------------------------------------------
// 5. DELETE: Başarılı Silme Testi (Kendi yorumunu siler)
// ---------------------------------------------------------
it('allows users to delete their own comment', function () {
    $owner = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($owner)->deleteJson("/api/v1/comments/{$comment->id}");

    $response->assertStatus(200);
    
    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});