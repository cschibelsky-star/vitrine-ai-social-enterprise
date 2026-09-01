<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            $table->string('role', 32)->default('admin')->after('password');
            $table->string('status', 32)->default('active')->after('role');
            $table->index(['client_id', 'role', 'status']);
        });

        Schema::create('client_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('plan_code', 40);
            $table->string('status', 32)->default('active');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('core_subscription_id')->nullable();
            $table->string('source', 32)->default('core');
            $table->timestamps();
            $table->index(['client_id', 'status']);
        });

        Schema::create('client_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('module_code', 64);
            $table->string('status', 32)->default('active');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->unique(['client_id', 'module_code']);
        });

        Schema::create('client_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('balance_type', 40);
            $table->decimal('granted', 14, 2)->default(0);
            $table->decimal('consumed', 14, 2)->default(0);
            $table->decimal('available', 14, 2)->default(0);
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->timestamps();
            $table->unique(['client_id', 'balance_type', 'period_start']);
            $table->index(['client_id', 'balance_type']);
        });

        Schema::create('consumption_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('balance_type', 40);
            $table->string('movement_type', 24);
            $table->decimal('amount', 14, 2);
            $table->nullableMorphs('reference');
            $table->string('description')->nullable();
            $table->decimal('balance_before', 14, 2)->nullable();
            $table->decimal('balance_after', 14, 2)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['client_id', 'balance_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumption_ledgers');
        Schema::dropIfExists('client_balances');
        Schema::dropIfExists('client_modules');
        Schema::dropIfExists('client_subscriptions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('brand_id');
            $table->dropConstrainedForeignId('client_id');
            $table->dropColumn(['role', 'status']);
        });
    }
};
