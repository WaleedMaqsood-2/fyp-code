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
        Schema::create('forensic_reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('analyst_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('fir_id')->constrained('fir_reports')->onDelete('cascade');
    $table->text('findings');
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forensic_reviews');
    }
};
