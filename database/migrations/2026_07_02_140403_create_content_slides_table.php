<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_project_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('slide_number');
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->longText('visual_instruction')->nullable();
            $table->string('layout_type')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->unique(['content_project_id', 'slide_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_slides');
    }
};
