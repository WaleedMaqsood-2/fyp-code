<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReportExportsForeignKey extends Migration
{
    public function up()
    {
        Schema::table('report_exports', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['review_id']);
            
            // Add new foreign key to complaints table
            $table->foreign('review_id')
                  ->references('id')
                  ->on('complaints')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('report_exports', function (Blueprint $table) {
            $table->dropForeign(['review_id']);
            
            // Restore original foreign key if needed
            $table->foreign('review_id')
                  ->references('id')
                  ->on('forensic_reviews')
                  ->onDelete('cascade');
        });
    }
}