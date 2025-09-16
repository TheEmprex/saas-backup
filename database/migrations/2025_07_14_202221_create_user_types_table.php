<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('user_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name'); // 'chatter', 'ofm_agency', 'chatting_agency'
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('required_fields')->nullable(); // JSON field for dynamic requirements
            $table->boolean('requires_kyc')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_types');
    }
};
