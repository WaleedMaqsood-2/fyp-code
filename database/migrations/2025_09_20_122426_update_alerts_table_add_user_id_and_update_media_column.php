<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    // database/migrations/xxxx_xx_xx_add_media_to_alerts_table.php
// database/migrations/xxxx_xx_xx_add_media_and_user_to_alerts_table.php

    public function up()
    {
        Schema::table('public_alerts', function (Blueprint $table) {
            // Agar user_id column exist nahi karta tabhi add karein
            if (!Schema::hasColumn('public_alerts', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }

            // Agar media column exist karta hai to change karein warna naya add karein
            if (Schema::hasColumn('public_alerts', 'media')) {
                $table->json('media')->nullable()->change();
            } else {
                $table->json('media')->nullable()->after('type');
            }
        });
    }

    public function down()
    {
        Schema::table('public_alerts', function (Blueprint $table) {
            if (Schema::hasColumn('public_alerts', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('public_alerts', 'media')) {
                $table->dropColumn('media');
            }
        });
    }
};



