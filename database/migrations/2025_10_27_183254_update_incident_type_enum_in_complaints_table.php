<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->enum('incident_type', [
                'Theft',
                'Assault',
                'Fraud',
                'Cybercrime',
                'Harassment',
                'Murder',
                'Kidnapping',
                'Vandalism',
                'Missing Person',
                'Drug Related',
                'Traffic Violation',
                'Other'
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Revert back to varchar if needed
            $table->string('incident_type')->change();
        });
    }
};
