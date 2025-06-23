<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
 Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('username')->unique(); // ← Tambahkan ini
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('unit_bagian')->nullable();
    $table->string('level_gol')->nullable();
    $table->string('wilayah')->nullable();
    $table->string('unit_kerja')->nullable();
    $table->string('foto')->nullable();
    $table->enum('role', ['super_user', 'divisi_user', 'user_umum'])->default('user_umum');
    $table->rememberToken();
    $table->timestamps();
});
    }


    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
