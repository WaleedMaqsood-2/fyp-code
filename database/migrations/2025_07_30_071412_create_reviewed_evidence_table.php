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
        Schema::create('reviewed_evidence', function (Blueprint $table) {
    $table->id();
    $table->foreignId('review_id')->constrained('forensic_reviews')->onDelete('cascade');
    $table->foreignId('evidence_id')->constrained('evidence')->onDelete('cascade');
    $table->text('comments')->nullable();
    $table->boolean('suspicious')->default(false);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviewed_evidence_tabl');
    }
};
