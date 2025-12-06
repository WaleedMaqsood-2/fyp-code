<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // migration file میں
public function up()
{
    Schema::table('evidence_segments', function (Blueprint $table) {
        $table->string('file_extension')->nullable()->after('segment_file');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evidence_segments', function (Blueprint $table) {
            $table->dropColumn('file_extension');
        });
    }
};
