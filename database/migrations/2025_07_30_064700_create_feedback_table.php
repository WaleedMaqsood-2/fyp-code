<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who gave the feedback
            $table->enum('ai_type', ['transcription', 'face_detection', 'summarization']);
            $table->tinyInteger('rating')->unsigned()->comment('1 = poor, 5 = excellent');
            $table->text('feedback_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
