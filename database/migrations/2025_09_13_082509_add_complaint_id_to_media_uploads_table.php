<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_uploads', function (Blueprint $table) {
            $table->foreignId('complaint_id')
                  ->nullable()
                  ->constrained('complaints')
                  ->onDelete('cascade')
                  ->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('media_uploads', function (Blueprint $table) {
            $table->dropForeign(['complaint_id']);
            $table->dropColumn('complaint_id');
        });
    }
};
