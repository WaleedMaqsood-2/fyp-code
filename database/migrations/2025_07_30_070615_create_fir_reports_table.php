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
       Schema::create('fir_reports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('police_id')->constrained('users')->onDelete('cascade'); // officer
    $table->string('title');
    $table->text('description');
    $table->string('location');
    $table->timestamp('incident_date');
    $table->enum('status', ['pending', 'in_progress', 'closed'])->default('pending');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fir_reports');
    }
};
