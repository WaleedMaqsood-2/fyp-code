<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evidence_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');
            $table->foreignId('media_id')->constrained('media_uploads')->onDelete('cascade'); // Original evidence
            $table->string('segment_file'); // Path to segmented file
            $table->string('start_time');   // hh:mm:ss
            $table->string('end_time');     // hh:mm:ss
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidence_segments');
    }
};
