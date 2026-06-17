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
        Schema::create('zmeyka_rezultati', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polzovatel_id')->constrained('polzovateli')->onDelete('cascade');
            $table->integer('dlina');
            $table->integer('ochki');
            $table->integer('vremya_sek');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zmeyka_rezultati');
    }
};
