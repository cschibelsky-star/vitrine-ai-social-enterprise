<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'client_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('client_id')->nullable()->after('id')->constrained('clients')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('users', 'brand_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('brand_id')->nullable()->after('client_id')->constrained('brands')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('client')->after('password')->index();
            });
        }

        if (! Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('status')->default('active')->after('role')->index();
            });
        }
    }

    public function down(): void
    {
        // These columns are owned by the earlier entitlement migration.
        // This compatibility migration must never remove shared access fields.
    }
};
