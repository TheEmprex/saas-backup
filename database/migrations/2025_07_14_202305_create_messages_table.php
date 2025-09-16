<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_post_id')->nullable()->constrained()->onDelete('cascade'); // Related job if applicable
            $table->foreignId('job_application_id')->nullable()->constrained()->onDelete('cascade'); // Related application if applicable

            $table->text('message_content');
            $table->json('attachments')->nullable(); // Array of file paths
            $table->enum('message_type', ['text', 'file', 'system'])->default('text');

            // Message status
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_archived')->default(false);

            // Thread grouping
            $table->string('thread_id')->nullable(); // For grouping related messages
            $table->foreignId('parent_message_id')->nullable()->constrained('messages')->onDelete('cascade'); // For replies

            $table->timestamps();

            $table->index(['sender_id', 'recipient_id']);
            $table->index(['thread_id', 'created_at']);
            $table->index(['job_post_id', 'created_at']);
            $table->index(['is_read', 'recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
