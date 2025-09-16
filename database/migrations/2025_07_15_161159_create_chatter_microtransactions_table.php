<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('chatter_microtransactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->string('type'); // 'premium_job_unlock'
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatter_microtransactions');
    }
};
