<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waitlist_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 190)->unique();
            $table->string('whatsapp', 30);
            $table->string('company', 160)->nullable();
            $table->string('source', 80)->default('landing_lista_vip');
            $table->boolean('consent')->default(false);
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->index(['source', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlist_leads');
    }
};
