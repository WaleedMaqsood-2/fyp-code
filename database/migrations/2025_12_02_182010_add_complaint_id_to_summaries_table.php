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
        Schema::table('summaries', function (Blueprint $table) {

            // Add new complaint_id column (after user_id or wherever you want)
            $table->unsignedBigInteger('complaint_id')->after('id');

            // Add foreign key
            $table->foreign('complaint_id')
                ->references('id')
                ->on('complaints')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('summaries', function (Blueprint $table) {

            // Drop foreign key first
            $table->dropForeign(['complaint_id']);

            // Drop column
            $table->dropColumn('complaint_id');
        });
    }
};
