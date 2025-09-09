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
        Schema::create('criminal_records_public', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('crime_type');
    $table->string('location');
    $table->timestamp('incident_date');
    $table->text('short_description');
    $table->string('photo')->nullable(); // optional photo
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criminal_records_public');
    }
};
