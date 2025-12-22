<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_similarities', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to complaints
            $table->unsignedBigInteger('complaint_id');
            $table->foreign('complaint_id')
                  ->references('id')
                  ->on('complaints')
                  ->onDelete('cascade');

            $table->boolean('has_similar')->default(false);
            $table->decimal('similarity_score', 5, 2)->nullable();
            $table->text('matched_text')->nullable();
            $table->timestamp('checked_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_similarities');
    }
};
