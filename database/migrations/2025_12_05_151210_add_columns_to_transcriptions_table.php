<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transcriptions', function (Blueprint $table) {
            // Agar yeh columns nahi hain to add karein
            if (!Schema::hasColumn('transcriptions', 'original_text')) {
                $table->text('original_text')->nullable()->after('transcript');
            }
            
            if (!Schema::hasColumn('transcriptions', 'roman_text')) {
                $table->text('roman_text')->nullable()->after('original_text');
            }
            
            if (!Schema::hasColumn('transcriptions', 'audio_path')) {
                $table->string('audio_path')->nullable()->after('roman_text');
            }
            
            if (!Schema::hasColumn('transcriptions', 'language')) {
                $table->string('language')->default('ur')->after('audio_path');
            }
            
            if (!Schema::hasColumn('transcriptions', 'status')) {
                $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'verified'])
                      ->default('pending')
                      ->after('language');
            }
            
            if (!Schema::hasColumn('transcriptions', 'confidence_score')) {
                $table->float('confidence_score')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('transcriptions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('confidence_score');
                $table->foreign('user_id')->references('id')->on('users');
            }
        });
        
        Schema::table('transcription_verifications', function (Blueprint $table) {
            if (!Schema::hasColumn('transcription_verifications', 'transcription_id')) {
                $table->unsignedBigInteger('transcription_id')->after('id');
                $table->foreign('transcription_id')->references('id')->on('transcriptions')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('transcription_verifications', 'corrected_roman')) {
                $table->text('corrected_roman')->nullable()->after('corrected_text');
            }
            
            if (!Schema::hasColumn('transcription_verifications', 'notes')) {
                $table->text('notes')->nullable()->after('approved');
            }
            
            if (!Schema::hasColumn('transcription_verifications', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('notes');
            }
        });
    }

    public function down()
    {
        Schema::table('transcriptions', function (Blueprint $table) {
            $table->dropColumn(['original_text', 'roman_text', 'audio_path', 
                               'language', 'status', 'confidence_score', 'user_id']);
        });
        
        Schema::table('transcription_verifications', function (Blueprint $table) {
            $table->dropForeign(['transcription_id']);
            $table->dropColumn(['transcription_id', 'corrected_roman', 'notes', 'verified_at']);
        });
    }
};