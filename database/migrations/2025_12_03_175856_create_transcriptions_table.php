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
    Schema::create('transcriptions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('complaint_id');
        $table->unsignedBigInteger('media_id');
        $table->text('transcript')->nullable();
        $table->timestamps();

        $table->foreign('complaint_id')
              ->references('id')
              ->on('complaints')
              ->onDelete('cascade');
        $table->foreign('media_id')
              ->references('id')
              ->on('media_uploads')
              ->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcriptions');
    }
};
