<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energis', function (Blueprint $table) {
            $table->id();
            $table->string('kantor')->nullable();
            $table->string('bulan')->nullable();
            $table->integer('tahun')->nullable();
            $table->double('listrik')->nullable();
            $table->double('air')->nullable();
            $table->double('bbm')->nullable();
            $table->double('kertas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energis');
    }
};

