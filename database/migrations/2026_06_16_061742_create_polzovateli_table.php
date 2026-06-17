<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('polzovateli', function (Blueprint $table) {
            $table->id();
            $table->string('login', 50)->unique();
            $table->string('parol', 255);
            $table->string('imya', 100)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->foreignId('rol_id')->constrained('roli')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polzovateli');
    }
};
