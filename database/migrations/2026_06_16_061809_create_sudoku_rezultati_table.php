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
        Schema::create('sudoku_rezultati', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polzovatel_id')->constrained('polzovateli')->onDelete('cascade');
            $table->enum('slozhnost', ['easy', 'medium', 'hard']);
            $table->integer('ochki');
            $table->integer('vremya_sek');
            $table->boolean('zaversheno')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sudoku_rezultati');
    }
};
