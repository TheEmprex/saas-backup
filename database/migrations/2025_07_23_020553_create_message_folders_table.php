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
        Schema::create('message_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('color', 7)->default('#6366f1'); // Hex color code
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'name']); // User can't have duplicate folder names
        });
        
        // Add folder_id to messages table to organize conversations
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('folder_id')->nullable()->constrained('message_folders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['folder_id']);
            $table->dropColumn('folder_id');
        });
        
        Schema::dropIfExists('message_folders');
    }
};
