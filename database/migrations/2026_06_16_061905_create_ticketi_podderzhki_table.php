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
        Schema::create('ticketi_podderzhki', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polzovatel_id')->constrained('polzovateli')->onDelete('cascade');
            $table->string('tema', 255);
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->foreignId('moderator_id')->nullable()->constrained('polzovateli')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticketi_podderzhki');
    }
};
