<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('action_type', ['transcription', 'face_detection', 'summarization']);
            $table->string('input_file')->nullable(); // filename or path of audio/image
            $table->longText('output_data')->nullable(); // result of AI processing
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_activities');
    }
};
