<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::table('media_uploads', function (Blueprint $table) {
        // Make sure complaint_id exists
        if (!Schema::hasColumn('media_uploads', 'complaint_id')) {
            $table->unsignedBigInteger('complaint_id')->nullable()->after('user_id');
        }

        // Add foreign key
        $table->foreign('complaint_id')
              ->references('id')
              ->on('complaints')
              ->onDelete('cascade')
              ->onUpdate('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
 public function down()
{
    
}


};
