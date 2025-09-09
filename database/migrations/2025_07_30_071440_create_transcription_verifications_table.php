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
        Schema::create('transcription_verifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');
    $table->foreignId('analyst_id')->constrained('users')->onDelete('cascade');
    $table->longText('corrected_text');
    $table->boolean('approved')->default(false);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcription_verifications');
    }
};
