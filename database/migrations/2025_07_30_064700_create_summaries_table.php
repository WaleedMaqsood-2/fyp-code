<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->text('summary_text'); // actual summary content
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who created the summary
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // who approved it
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('summaries');
    }
};
