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
        Schema::create('case_assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('fir_id')->constrained('fir_reports')->onDelete('cascade');
    $table->foreignId('police_id')->constrained('users')->onDelete('cascade'); // assigned police
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_assignments');
    }
};
