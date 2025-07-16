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
            $table->double('daya_listrik')->nullable(); // Added field
            $table->double('air')->nullable();
            $table->double('kertas')->nullable();
            
            // Separate fuel type columns
            $table->double('pertalite')->nullable()->default(0);
            $table->double('pertamax')->nullable()->default(0);
            $table->double('solar')->nullable()->default(0);
            $table->double('dexlite')->nullable()->default(0);
            $table->double('pertamina_dex')->nullable()->default(0);
            
            // Keep the old bbm column for backward compatibility
            $table->double('bbm')->nullable();
            $table->text('jenis_bbm')->nullable(); // Store fuel types as string
            
            // Add user relationship
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energis');
    }
};