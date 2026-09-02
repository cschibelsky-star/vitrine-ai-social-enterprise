<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consumption_ledgers', function (Blueprint $table) {
            $table->string('unit', 32)->nullable()->after('amount');
            $table->decimal('unit_price', 14, 4)->nullable()->after('unit');
            $table->decimal('charged_amount', 14, 2)->nullable()->after('unit_price');
            $table->json('metadata')->nullable()->after('charged_amount');
            $table->index(['client_id', 'movement_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('consumption_ledgers', function (Blueprint $table) {
            $table->dropIndex(['client_id', 'movement_type', 'created_at']);
            $table->dropColumn(['unit', 'unit_price', 'charged_amount', 'metadata']);
        });
    }
};
