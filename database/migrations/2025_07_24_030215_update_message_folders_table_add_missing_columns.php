<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('message_folders', function (Blueprint $table) {
            if (!Schema::hasColumn('message_folders', 'description')) {
                $table->text('description')->nullable()->after('color');
            }
            if (!Schema::hasColumn('message_folders', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('sort_order');
            }
        });

        // Create default folders for existing users who don't have them
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $existingFolder = DB::table('message_folders')
                ->where('user_id', $user->id)
                ->where('name', 'Inbox')
                ->first();
                
            if (!$existingFolder) {
                DB::table('message_folders')->insert([
                    'user_id' => $user->id,
                    'name' => 'Inbox',
                    'color' => '#3B82F6',
                    'description' => 'Default inbox folder',
                    'sort_order' => 0,
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_folders', function (Blueprint $table) {
            $table->dropColumn(['description', 'is_default']);
        });
    }
};
