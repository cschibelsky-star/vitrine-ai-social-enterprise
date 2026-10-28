<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Composite parent identity ensures operations cannot reference another tenant's project.
        // Legacy projects without a client remain untouched but cannot be published via this table.
        Schema::table('content_projects', function (Blueprint $table) {
            $table->unique(['id', 'client_id'], 'content_projects_id_client_unique');
        });

        Schema::create('social_publication_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_project_id');
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->string('channel', 32);
            $table->string('content_version', 64);
            $table->string('idempotency_key', 64)->unique();
            $table->string('status', 32)->default('queued');
            $table->string('provider_container_id', 191)->nullable();
            $table->string('external_id', 191)->nullable();
            $table->string('last_error_code', 100)->nullable();
            $table->text('last_error_message')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['client_id', 'content_project_id', 'channel', 'content_version'],
                'social_publication_operation_identity'
            );
            $table->foreign(['content_project_id', 'client_id'], 'social_pub_project_tenant_fk')
                ->references(['id', 'client_id'])->on('content_projects')->restrictOnDelete();
            $table->index(['status', 'updated_at']);
            $table->index(['client_id', 'channel', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_publication_operations');
        Schema::table('content_projects', function (Blueprint $table) {
            $table->dropUnique('content_projects_id_client_unique');
        });
    }
};
