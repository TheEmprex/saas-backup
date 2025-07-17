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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->string('payment_method')->default('card')->after('expires_at');
            $table->string('payment_id')->nullable()->after('payment_method');
            $table->string('crypto_currency')->nullable()->after('payment_id');
            $table->string('crypto_address')->nullable()->after('crypto_currency');
            $table->decimal('crypto_amount', 20, 8)->nullable()->after('crypto_address');
            $table->string('crypto_transaction_id')->nullable()->after('crypto_amount');
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'expired'])->default('pending')->after('crypto_transaction_id');
            $table->timestamp('payment_confirmed_at')->nullable()->after('payment_status');
            $table->json('payment_metadata')->nullable()->after('payment_confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_id',
                'crypto_currency',
                'crypto_address',
                'crypto_amount',
                'crypto_transaction_id',
                'payment_status',
                'payment_confirmed_at',
                'payment_metadata'
            ]);
        });
    }
};
