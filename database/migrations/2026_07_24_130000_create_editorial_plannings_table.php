<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('editorial_plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('content_project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('theme');
            $table->string('objective')->nullable();
            $table->string('channel')->default('instagram');
            $table->string('format')->default('post');
            $table->date('planned_for');
            $table->unsignedTinyInteger('priority')->default(3);
            $table->string('status')->default('planned');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['client_id', 'planned_for']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editorial_plannings');
    }
};
