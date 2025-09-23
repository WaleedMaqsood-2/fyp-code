<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_alerts', function (Blueprint $table) {
            // media column add karein
            $table->enum('media',['image', 'video', 'audio', 'document', 'archive'])->default('document')->nullable()->after('message');

            // type column ko modify karein
            $table->enum('type', [
                'notice',
                'crime_alert',
                'helpline',
                'Informational',
                'Warning',
                'Critical'
            ])->default('Informational')->change();
        });
    }

    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn('media');

            // wapis original enum par
            $table->enum('type', [
                'notice',
                'crime_alert',
                'helpline'
            ])->default('notice')->change();
        });
    }
};
