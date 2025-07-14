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
        Schema::table('energis', function (Blueprint $table) {
            // Tambah kolom user_id setelah kolom created_at
            $table->unsignedBigInteger('user_id')->nullable()->after('created_at');

            // Relasi foreign key ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('energis', function (Blueprint $table) {
            // Hapus relasi foreign key dulu sebelum hapus kolom
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
