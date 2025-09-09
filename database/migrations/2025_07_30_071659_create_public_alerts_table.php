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
       Schema::create('public_alerts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('message');
    $table->enum('type', ['notice', 'crime_alert', 'helpline'])->default('notice');
    $table->timestamp('visible_until')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_alerts');
    }
};
