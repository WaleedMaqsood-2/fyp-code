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
       Schema::create('complaint_status_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');
    $table->string('status'); // e.g., received, under_review, resolved
    $table->text('note')->nullable();
    $table->timestamp('changed_at')->useCurrent();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_status_logs');
    }
};
