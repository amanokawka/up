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
        Schema::create('soobsheniya_ticketov', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('ticketi_podderzhki')->onDelete('cascade');
            $table->foreignId('polzovatel_id')->constrained('polzovateli')->onDelete('cascade');
            $table->text('tekst');
            $table->boolean('ot_personala')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soobsheniya_ticketov');
    }
};
