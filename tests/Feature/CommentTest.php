<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->postJson('/api/comments', [
                'user_id' => $user->id,
                'post_id' => $post->id,
                'content' => 'Test Comment Content'
            ]);

        $response->assertStatus(201);
    }

    public function test_can_update_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = \App\Models\Comment::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'content' => 'Original Content'
        ]);

        $response = $this->actingAs($user)
            ->putJson("/api/comments/{$comment->id}", [
                'content' => 'Updated Content'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated Content'
        ]);
    }

    public function test_can_delete_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = \App\Models\Comment::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'content' => 'To be deleted'
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id
        ]);
    }
}
