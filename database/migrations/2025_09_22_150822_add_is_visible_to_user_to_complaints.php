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
        // database/migrations/xxxx_xx_xx_add_is_visible_to_user_to_complaints.php
Schema::table('complaints', function (Blueprint $table) {
    $table->boolean('is_visible_to_user')->default(1)->after('status');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn('is_visible_to_user');
        });
    }
};
