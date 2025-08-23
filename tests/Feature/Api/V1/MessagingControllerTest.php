<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MessagingControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected User $otherUser;
    protected Conversation $conversation;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        
        $this->conversation = Conversation::factory()->create();
        $this->conversation->participants()->attach([$this->user->id, $this->otherUser->id]);
    }

    public function test_can_list_conversations()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/conversations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'title',
                            'last_message',
                            'participants',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'pagination'
                ]
            ]);
    }

    public function test_can_create_conversation()
    {
        $newUser = User::factory()->create();
        
        $data = [
            'participant_ids' => [$newUser->id],
            'initial_message' => 'Hello there!'
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/conversations', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'participants',
                    'created_at'
                ]
            ]);

        $this->assertDatabaseHas('conversations', [
            'id' => $response->json('data.id')
        ]);
    }

    public function test_can_get_conversation_messages()
    {
        $message = Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/conversations/{$this->conversation->id}/messages");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'content',
                            'type',
                            'sender',
                            'created_at'
                        ]
                    ],
                    'pagination'
                ]
            ]);
    }

    public function test_can_send_message()
    {
        $data = [
            'content' => 'Test message',
            'type' => 'text'
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/conversations/{$this->conversation->id}/messages", $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'content',
                    'type',
                    'sender',
                    'created_at'
                ]
            ]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id,
            'content' => 'Test message'
        ]);
    }

    public function test_cannot_access_unauthorized_conversation()
    {
        $unauthorizedUser = User::factory()->create();
        
        $response = $this->actingAs($unauthorizedUser)
            ->getJson("/api/v1/conversations/{$this->conversation->id}/messages");

        $response->assertStatus(403);
    }

    public function test_can_mark_messages_as_read()
    {
        $message = Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->otherUser->id
        ]);

        $data = [
            'message_ids' => [$message->id]
        ];

        $response = $this->actingAs($this->user)
            ->patchJson("/api/v1/conversations/{$this->conversation->id}/messages/read", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('message_reads', [
            'message_id' => $message->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_validation_fails_with_invalid_data()
    {
        $data = [
            'content' => '', // Empty content should fail
            'type' => 'invalid_type'
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/conversations/{$this->conversation->id}/messages", $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content', 'type']);
    }

    public function test_can_search_conversations()
    {
        // Create a conversation with a specific message
        $searchableMessage = Message::factory()->create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->user->id,
            'content' => 'This is a searchable message'
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/conversations?search=searchable');

        $response->assertStatus(200)
            ->assertJsonPath('data.items.0.id', $this->conversation->id);
    }

    public function test_rate_limiting_prevents_spam()
    {
        // Send multiple messages rapidly
        for ($i = 0; $i < 10; $i++) {
            $response = $this->actingAs($this->user)
                ->postJson("/api/v1/conversations/{$this->conversation->id}/messages", [
                    'content' => "Message {$i}",
                    'type' => 'text'
                ]);
            
            if ($i < 5) {
                $response->assertStatus(201);
            } else {
                // Should be rate limited after 5 messages
                $response->assertStatus(429);
                break;
            }
        }
    }
}
