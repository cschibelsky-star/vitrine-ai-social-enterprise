<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->longText('idea');
            $table->string('content_type')->default('post');
            $table->string('generation_method')->default('from_scratch');
            $table->string('objective')->default('engagement');
            $table->string('format')->default('post_portrait');
            $table->string('channel')->default('instagram');
            $table->string('status')->default('draft');
            $table->longText('caption')->nullable();
            $table->text('cta')->nullable();
            $table->text('hashtags')->nullable();
            $table->decimal('score', 4, 2)->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('objective');
            $table->index('format');
            $table->index('scheduled_at');
            $table->index(['client_id', 'brand_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_projects');
    }
};
