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
        Schema::table('complaint_status_logs', function (Blueprint $table) {

            // 🔹 Add new columns if they don’t exist
            if (!Schema::hasColumn('complaint_status_logs', 'police_officer')) {
                $table->foreignId('police_officer')
                    ->after('complaint_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('cascade');
            }

            if (!Schema::hasColumn('complaint_status_logs', 'forwarded_to')) {
                $table->foreignId('forwarded_to')
                    ->nullable()
                    ->after('police_officer')
                    ->constrained('users')
                    ->onDelete('set null');
            }

            

            // 🔸 Modify status column to enum
            $table->enum('status', [
                'received',
                'under_review',
                'forwarded',
                'analyzing',
                'completed',
                'rejected'
            ])->default('received')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaint_status_logs', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['police_officer']);
            $table->dropForeign(['forwarded_to']);

            // Then drop columns
            $table->dropColumn(['police_officer', 'forwarded_to']);

            // Restore status to simple string if needed
            // $table->string('status')->change();
        });
    }
};
