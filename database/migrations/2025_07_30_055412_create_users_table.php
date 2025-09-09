<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('cnic')->unique();
    $table->string('contact_number')->unique();
    $table->string('profile_image')->nullable();
    $table->string('otp')->nullable();
    $table->timestamp('otp_expires_at')->nullable();
    $table->boolean('is_verified')->default(false);
    $table->enum('reg_status', ['pending', 'Registered'])->default('pending');
    $table->enum('status', ['active', 'inactive','blocked','remove access'])->default('inactive');
    $table->timestamp('email_verified_at')->nullable();
    // $table->rememberToken();
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
