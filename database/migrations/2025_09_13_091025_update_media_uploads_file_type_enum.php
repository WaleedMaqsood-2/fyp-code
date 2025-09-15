<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_uploads', function (Blueprint $table) {
            // Update file_type enum to allow only valid values
            $table->enum('file_type', ['image', 'video', 'audio', 'document', 'pdf', 'jpg', 'mp4','png','gif','webp','zip','rar','txt','docx','xlsx'])->default('image')->change();
        });
    }

    public function down(): void
    {
        Schema::table('media_uploads', function (Blueprint $table) {
            // Revert back to previous enum if needed
            $table->enum('file_type', ['image', 'video', 'audio', 'document'])->default('image')->change();
        });
    }
};
