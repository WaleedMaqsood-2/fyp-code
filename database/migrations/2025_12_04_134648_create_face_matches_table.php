<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaceMatchesTable extends Migration
{
    public function up()
    {
        Schema::create('face_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->foreignId('media_id')->nullable()->constrained('media_uploads')->onDelete('cascade');
            $table->string('reference_image_path'); // Uploaded reference image
            $table->string('matched_image_path'); // Matched image from database
            $table->decimal('confidence', 5, 2); // Match confidence percentage
            $table->json('match_details')->nullable(); // Additional match data
            $table->foreignId('analyst_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('face_matches');
    }
}