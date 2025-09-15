<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('user_id');
            $table->string('location')->nullable()->after('description');
            $table->string('incident_type')->nullable()->after('location');
            $table->string('severity')->nullable()->after('incident_type');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['subject', 'location', 'incident_type', 'severity']);
        });
    }
};
