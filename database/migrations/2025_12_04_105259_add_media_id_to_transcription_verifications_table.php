<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMediaIdToTranscriptionVerificationsTable extends Migration
{
    public function up()
    {
        Schema::table('transcription_verifications', function (Blueprint $table) {
            // Add media_id column
            $table->unsignedBigInteger('media_id')->nullable()->after('complaint_id');
            
            // Add foreign key constraint
            $table->foreign('media_id')
                  ->references('id')
                  ->on('media_uploads')
                  ->onDelete('cascade');
                  
            // Add index
            $table->index('media_id');
        });
    }

    public function down()
    {
        Schema::table('transcription_verifications', function (Blueprint $table) {
            $table->dropForeign(['media_id']);
            $table->dropIndex(['media_id']);
            $table->dropColumn('media_id');
        });
    }
}