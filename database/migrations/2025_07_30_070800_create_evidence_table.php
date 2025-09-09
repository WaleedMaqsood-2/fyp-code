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
       Schema::create('evidence', function (Blueprint $table) {
    $table->id();
    $table->foreignId('fir_id')->constrained('fir_reports')->onDelete('cascade');
    $table->string('file_type'); // image, video, doc
    $table->string('file_path');
    $table->text('notes')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidence');
    }
};
