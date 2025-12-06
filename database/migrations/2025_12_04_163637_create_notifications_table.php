<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type'); // notification type
            $table->string('module')->nullable(); // admin, police, forensic, public
            $table->string('link')->nullable(); // URL to redirect
            $table->json('data')->nullable(); // additional data
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'module']);
            $table->index(['type', 'created_at']);
            $table->index('module');
        });
        Schema::table('users', function (Blueprint $table) {
    if (!Schema::hasColumn('users', 'remember_token')) {
        $table->rememberToken()->nullable();
    }
});
        // Add notification_preferences to users table
        Schema::table('users', function (Blueprint $table) {
            $table->json('notification_preferences')->nullable()->after('remember_token');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notification_preferences');
        });
    }
};