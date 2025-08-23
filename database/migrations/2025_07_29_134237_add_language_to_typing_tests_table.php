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
        Schema::table('typing_tests', function (Blueprint $table) {
            $table->string('language', 10)->default('en')->after('title');
            $table->index(['language', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('typing_tests', function (Blueprint $table) {
            $table->dropIndex(['language', 'is_active']);
            $table->dropColumn('language');
        });
    }
};
