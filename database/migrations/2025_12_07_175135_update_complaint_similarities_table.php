<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint_similarities', function (Blueprint $table) {

            // If column doesn't already exist — create it nullable so old rows don't break
            if (!Schema::hasColumn('complaint_similarities', 'similar_complaint_id')) {
                $table->foreignId('similar_complaint_id')->nullable()->after('complaint_id');
            }

            // Extra fields (will never break)
            $table->json('key_phrases')->nullable();
            $table->string('confidence_level')->default('medium'); // high / medium / low
            $table->json('metadata')->nullable();
            $table->timestamp('checked_at')->nullable();

            // Add timestamps if missing
            if (!Schema::hasColumn('complaint_similarities', 'created_at')) {
                $table->timestamps();
            }
        });

        // Foreign key add after ensuring there is no broken data
        Schema::table('complaint_similarities', function (Blueprint $table) {
            $table->foreign('similar_complaint_id')
                  ->references('id')->on('complaints')
                  ->onDelete('cascade');
        });
    }


    public function down(): void
    {
        Schema::table('complaint_similarities', function (Blueprint $table) {
            $table->dropForeign(['similar_complaint_id']);
            $table->dropColumn(['similar_complaint_id','key_phrases','confidence_level','metadata','checked_at']);
        });
    }
};
