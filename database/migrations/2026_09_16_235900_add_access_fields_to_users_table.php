<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('id')->constrained('clients')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->after('client_id')->constrained('brands')->nullOnDelete();
            $table->string('role')->default('client')->after('password')->index();
            $table->string('status')->default('active')->after('role')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['client_id']);
            $table->dropColumn(['brand_id', 'client_id', 'role', 'status']);
        });
    }
};
