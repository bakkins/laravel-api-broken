<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'title' => 'Test Post',
                'body' => 'Test Body',
                'password' => 'secret'
            ]);

        $response->assertStatus(201);
    }

    public function test_can_update_post(): void
    {
        $user = User::factory()->create();
        $post = \App\Models\Post::create([
            'user_id' => $user->id,
            'title' => 'Original Title',
            'body' => 'Original Body'
        ]);

        $response = $this->actingAs($user)
            ->putJson("/api/posts/{$post->id}", [
                'title' => 'Updated Title',
                'body' => 'Updated Body'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title'
        ]);
    }

    public function test_can_delete_post(): void
    {
        $user = User::factory()->create();
        $post = \App\Models\Post::create([
            'user_id' => $user->id,
            'title' => 'To be deleted',
            'body' => '...'
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('posts', [
            'id' => $post->id
        ]);
    }
}
