<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Rename message_content to content
            $table->renameColumn('message_content', 'content');
            
            // Add conversation_id column for new messaging system
            $table->foreignId('conversation_id')->nullable()->constrained()->onDelete('cascade');
            
            // Add missing columns for the new messaging system
            $table->string('file_url')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->json('read_by')->nullable();
            $table->foreignId('reply_to_id')->nullable()->constrained('messages')->onDelete('cascade');
            $table->timestamp('edited_at')->nullable();
            $table->integer('call_duration')->nullable();
            $table->json('reactions')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn([
                'conversation_id',
                'file_url',
                'file_name', 
                'file_size',
                'read_by',
                'reply_to_id',
                'edited_at',
                'call_duration',
                'reactions',
                'metadata',
                'deleted_at'
            ]);
            
            // Rename content back to message_content
            $table->renameColumn('content', 'message_content');
        });
    }
};
