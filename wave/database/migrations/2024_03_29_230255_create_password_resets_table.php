<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('password_resets', function (Blueprint $table): void {
            $table->string('email', 191)->index(); // The email column is indexed for faster lookups.
            $table->string('token', 191); // The token column stores the password reset token.
            $table->timestamp('created_at')->nullable(); // The created_at column can be null and records when the password reset token was created.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};
