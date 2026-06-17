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
        Schema::create('naidi_paru_rezultati', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polzovatel_id')->constrained('polzovateli')->onDelete('cascade');
            $table->integer('kolichestvo_par');
            $table->integer('hodi');
            $table->integer('vremya_sek');
            $table->integer('ochki');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('naidi_paru_rezultati');
    }
};
