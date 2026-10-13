<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::table('content_projects', function (Blueprint $table) {
            $table->json('publishing_asset')->nullable();
            $table->string('approved_fingerprint', 64)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('schedule_generation')->default(0);
        });
        Schema::create('publication_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_project_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('idempotency_key', 64)->unique();
            $table->string('status');
            $table->string('external_id')->nullable();
            $table->string('error_code')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('publication_attempts');
        Schema::table('content_projects', fn (Blueprint $table) => $table->dropColumn([
            'publishing_asset', 'approved_fingerprint', 'approved_at', 'approved_by', 'schedule_generation',
        ]));
    }
};
