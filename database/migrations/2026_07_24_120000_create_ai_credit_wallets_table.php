<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_credit_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('text_balance')->default(0);
            $table->unsignedInteger('image_balance')->default(0);
            $table->unsignedInteger('text_monthly_limit')->default(0);
            $table->unsignedInteger('image_monthly_limit')->default(0);
            $table->timestamp('renews_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_credit_wallet_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('operation', 40);
            $table->integer('amount');
            $table->string('idempotency_key')->unique();
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->decimal('estimated_cost', 12, 6)->nullable();
            $table->string('status', 20)->default('reserved');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_credit_transactions');
        Schema::dropIfExists('ai_credit_wallets');
    }
};
