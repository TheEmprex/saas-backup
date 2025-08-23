<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class MessagingSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $otherUser;
    protected $conversation;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        
        $this->otherUser = User::factory()->create([
            'name' => 'Other User',
            'email' => 'other@example.com'
        ]);

        // Create test conversation
        $this->conversation = Conversation::factory()->create([
            'type' => 'direct',
            'name' => null,
            'created_by' => $this->user->id
        ]);

        // Add participants
        $this->conversation->participants()->attach([
            $this->user->id => ['role' => 'admin', 'joined_at' => now()],
            $this->otherUser->id => ['role' => 'member', 'joined_at' => now()]
        ]);

        // Authenticate user
        Sanctum::actingAs($this->user, ['*']);
    }

    /** @test */
    public function it_can_list_user_conversations()
    {
        $response = $this->getJson('/api/marketplace/v1/conversations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'name',
                        'participants_count',
                        'last_message',
                        'unread_count',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total'
                ]
            ]);

        $this->assertCount(1, $response->json('data'));
    }

    /** @test */
    public function it_can_create_direct_conversation()
    {
        $newUser = User::factory()->create();

        $response = $this->postJson('/api/marketplace/v1/conversations', [
            'type' => 'direct',
            'participants' => [$newUser->id]
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'participants'
                ]
            ]);

        $this->assertDatabaseHas('conversations', [
            'type' => 'direct',
            'created_by' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_create_group_conversation()
    {
        $users = User::factory()->count(3)->create();

        $response = $this->postJson('/api/marketplace/v1/conversations', [
            'type' => 'group',
            'name' => 'Test Group',
            'participants' => $users->pluck('id')->toArray()
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'group',
                    'name' => 'Test Group'
                ]
            ]);
    }

    /** @test */
    public function it_validates_conversation_creation()
    {
        $response = $this->postJson('/api/marketplace/v1/conversations', [
            'type' => 'invalid_type'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'participants']);
    }

    /** @test */
    public function it_can_get_conversation_details()
    {
        $response = $this->getJson("/api/marketplace/v1/conversations/{$this->conversation->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'participants' => [
                        '*' => [
                            'id',
                            'name',
                            'avatar',
                            'role',
                            'joined_at'
                        ]
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_archive_conversation()
    {
        $response = $this->postJson("/api/marketplace/v1/conversations/{$this->conversation->id}/archive");

        $response->assertStatus(200);

        $this->assertDatabaseHas('conversation_user', [
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user->id,
            'archived_at' => now()
        ]);
    }

    /** @test */
    public function it_can_mute_conversation()
    {
        $response = $this->postJson("/api/marketplace/v1/conversations/{$this->conversation->id}/mute");

        $response->assertStatus(200);

        $this->assertDatabaseHas('conversation_user', [
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user->id,
            'muted_at' => now()
        ]);
    }

    /** @test */
    public function it_can_star_conversation()
    {
        $response = $this->postJson("/api/marketplace/v1/conversations/{$this->conversation->id}/star");

        $response->assertStatus(200);

        $this->assertDatabaseHas('conversation_user', [
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user->id,
            'starred_at' => now()
        ]);
    }

    /** @test */
    public function it_can_leave_conversation()
    {
        $response = $this->deleteJson("/api/marketplace/v1/conversations/{$this->conversation->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('conversation_user', [
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function it_can_send_message()
    {
        Queue::fake();

        $response = $this->postJson("/api/marketplace/v1/conversations/{$this->conversation->id}/messages", [
            'content' => 'Hello, this is a test message!',
            'type' => 'text'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'content',
                    'type',
                    'sender' => [
                        'id',
                        'name',
                        'avatar'
                    ],
                    'created_at'
                ]
            ]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id,
            'content' => 'Hello, this is a test message!',
            'type' => 'text'
        ]);
    }

    /** @test */
    public function it_can_send_reply_message()
    {
        $originalMessage = Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->otherUser->id,
            'content' => 'Original message'
        ]);

        $response = $this->postJson("/api/marketplace/v1/conversations/{$this->conversation->id}/messages", [
            'content' => 'This is a reply',
            'type' => 'text',
            'reply_to_id' => $originalMessage->id
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'reply_to_id' => $originalMessage->id
                ]
            ]);
    }

    /** @test */
    public function it_can_upload_file_message()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test.jpg', 800, 600);

        $response = $this->postJson("/api/marketplace/v1/messages/upload", [
            'file' => $file,
            'conversation_id' => $this->conversation->id,
            'type' => 'image'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'file_path',
                    'file_name',
                    'file_size'
                ]
            ]);

        Storage::disk('public')->assertExists('messages/' . $file->hashName());
    }

    /** @test */
    public function it_can_edit_message()
    {
        $message = Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id,
            'content' => 'Original content'
        ]);

        $response = $this->putJson("/api/marketplace/v1/messages/{$message->id}", [
            'content' => 'Edited content'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'content' => 'Edited content',
                    'edited' => true
                ]
            ]);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'content' => 'Edited content'
        ]);
    }

    /** @test */
    public function it_can_delete_message()
    {
        $message = Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id
        ]);

        $response = $this->deleteJson("/api/marketplace/v1/messages/{$message->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('messages', [
            'id' => $message->id
        ]);
    }

    /** @test */
    public function it_can_add_reaction_to_message()
    {
        $message = Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->otherUser->id
        ]);

        $response = $this->postJson("/api/marketplace/v1/messages/{$message->id}/reactions", [
            'emoji' => '👍'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('message_reactions', [
            'message_id' => $message->id,
            'user_id' => $this->user->id,
            'emoji' => '👍'
        ]);
    }

    /** @test */
    public function it_can_remove_reaction_from_message()
    {
        $message = Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->otherUser->id
        ]);

        // First add a reaction
        $message->reactions()->create([
            'user_id' => $this->user->id,
            'emoji' => '👍'
        ]);

        $response = $this->deleteJson("/api/marketplace/v1/messages/{$message->id}/reactions/👍");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('message_reactions', [
            'message_id' => $message->id,
            'user_id' => $this->user->id,
            'emoji' => '👍'
        ]);
    }

    /** @test */
    public function it_can_mark_messages_as_read()
    {
        $messages = Message::factory()->count(3)->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->otherUser->id
        ]);

        $response = $this->postJson("/api/marketplace/v1/messages/mark-read", [
            'conversation_id' => $this->conversation->id,
            'message_id' => $messages->last()->id
        ]);

        $response->assertStatus(200);

        foreach ($messages as $message) {
            $this->assertDatabaseHas('message_reads', [
                'message_id' => $message->id,
                'user_id' => $this->user->id
            ]);
        }
    }

    /** @test */
    public function it_can_search_messages()
    {
        Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id,
            'content' => 'This message contains the keyword Laravel'
        ]);

        Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id,
            'content' => 'This message is about PHP'
        ]);

        $response = $this->getJson("/api/marketplace/v1/messages/search?q=Laravel");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['content' => 'This message contains the keyword Laravel']);
    }

    /** @test */
    public function it_can_search_users()
    {
        User::factory()->create([
            'name' => 'John Developer',
            'email' => 'john.dev@example.com'
        ]);

        User::factory()->create([
            'name' => 'Jane Designer',
            'email' => 'jane.design@example.com'
        ]);

        $response = $this->getJson("/api/marketplace/v1/users/search?q=John");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'John Developer']);
    }

    /** @test */
    public function it_can_get_user_profile()
    {
        $response = $this->getJson("/api/marketplace/v1/users/{$this->otherUser->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'avatar',
                    'online',
                    'last_seen'
                ]
            ]);
    }

    /** @test */
    public function it_can_update_online_status()
    {
        $response = $this->postJson("/api/marketplace/v1/users/online-status", [
            'status' => 'online'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'last_seen' => now()
        ]);
    }

    /** @test */
    public function it_can_get_online_users()
    {
        // Set other user as online
        $this->otherUser->update(['last_seen' => now()]);

        $response = $this->getJson("/api/marketplace/v1/users/online");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'avatar',
                        'last_seen'
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_handles_typing_status()
    {
        Event::fake();

        $response = $this->postJson("/api/marketplace/v1/conversations/{$this->conversation->id}/typing", [
            'typing' => true
        ]);

        $response->assertStatus(200);

        // Verify typing status is cached
        $this->assertTrue(
            Cache::has("typing:{$this->conversation->id}:{$this->user->id}")
        );
    }

    /** @test */
    public function it_requires_authentication_for_all_endpoints()
    {
        $this->withoutAuthentication();

        $endpoints = [
            ['GET', '/api/marketplace/v1/conversations'],
            ['POST', '/api/marketplace/v1/conversations'],
            ['GET', "/api/marketplace/v1/conversations/{$this->conversation->id}"],
            ['POST', "/api/marketplace/v1/conversations/{$this->conversation->id}/messages"],
            ['GET', '/api/marketplace/v1/users/search'],
            ['POST', '/api/marketplace/v1/users/online-status']
        ];

        foreach ($endpoints as [$method, $endpoint]) {
            $response = $this->json($method, $endpoint);
            $response->assertStatus(401);
        }
    }

    /** @test */
    public function it_validates_conversation_access()
    {
        $unauthorizedConversation = Conversation::factory()->create();

        $response = $this->getJson("/api/marketplace/v1/conversations/{$unauthorizedConversation->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function it_handles_pagination_correctly()
    {
        // Create multiple conversations
        $conversations = Conversation::factory()->count(15)->create([
            'created_by' => $this->user->id
        ]);

        foreach ($conversations as $conversation) {
            $conversation->participants()->attach($this->user->id, [
                'role' => 'admin',
                'joined_at' => now()
            ]);
        }

        $response = $this->getJson('/api/marketplace/v1/conversations?per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'per_page',
                    'to',
                    'total'
                ]
            ]);
    }

    /** @test */
    public function it_caches_frequently_accessed_data()
    {
        // First request should hit the database
        $response1 = $this->getJson("/api/marketplace/v1/conversations/{$this->conversation->id}");
        
        // Second request should hit the cache
        $response2 = $this->getJson("/api/marketplace/v1/conversations/{$this->conversation->id}");

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Verify cache key exists
        $this->assertTrue(
            Cache::has("conversation:{$this->conversation->id}")
        );
    }

    /** @test */
    public function it_handles_file_upload_validation()
    {
        Storage::fake('public');

        // Test oversized file
        $largeFile = UploadedFile::fake()->create('large.pdf', 11000); // 11MB

        $response = $this->postJson("/api/marketplace/v1/messages/upload", [
            'file' => $largeFile,
            'conversation_id' => $this->conversation->id,
            'type' => 'file'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    /** @test */
    public function it_prevents_editing_others_messages()
    {
        $otherMessage = Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->otherUser->id,
            'content' => 'Other user message'
        ]);

        $response = $this->putJson("/api/marketplace/v1/messages/{$otherMessage->id}", [
            'content' => 'Trying to edit'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function it_handles_concurrent_message_sending()
    {
        Queue::fake();

        $responses = collect(range(1, 5))->map(function ($i) {
            return $this->postJson("/api/marketplace/v1/conversations/{$this->conversation->id}/messages", [
                'content' => "Message {$i}",
                'type' => 'text'
            ]);
        });

        // All messages should be created successfully
        $responses->each(function ($response) {
            $response->assertStatus(201);
        });

        $this->assertDatabaseCount('messages', 5);
    }

    protected function withoutAuthentication()
    {
        $this->app['auth']->forgetGuards();
        return $this;
    }
}
