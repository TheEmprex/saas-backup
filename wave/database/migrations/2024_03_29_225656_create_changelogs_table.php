<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('changelogs', function (Blueprint $table): void {
            $table->increments('id'); // Auto-incrementing UNSIGNED INTEGER (primary key)
            $table->string('title', 191); // VARCHAR equivalent column
            $table->string('description', 191); // VARCHAR equivalent column
            $table->text('body'); // TEXT column for larger text
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('changelogs');
    }
};
