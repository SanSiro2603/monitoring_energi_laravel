<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('energis', function (Blueprint $table) {
            // Cek apakah kolom belum ada sebelum menambahkan
            if (!Schema::hasColumn('energis', 'daya_listrik')) {
                $table->double('daya_listrik')->nullable()->after('listrik');
            }
            
            if (!Schema::hasColumn('energis', 'pertalite')) {
                $table->double('pertalite')->nullable()->default(0)->after('air');
            }
            
            if (!Schema::hasColumn('energis', 'pertamax')) {
                $table->double('pertamax')->nullable()->default(0)->after('pertalite');
            }
            
            if (!Schema::hasColumn('energis', 'solar')) {
                $table->double('solar')->nullable()->default(0)->after('pertamax');
            }
            
            if (!Schema::hasColumn('energis', 'dexlite')) {
                $table->double('dexlite')->nullable()->default(0)->after('solar');
            }
            
            if (!Schema::hasColumn('energis', 'pertamina_dex')) {
                $table->double('pertamina_dex')->nullable()->default(0)->after('dexlite');
            }
            
            if (!Schema::hasColumn('energis', 'jenis_bbm')) {
                $table->text('jenis_bbm')->nullable()->after('bbm');
            }
            
            // Add user relationship if not exists
            if (!Schema::hasColumn('energis', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('kertas');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('energis', function (Blueprint $table) {
            // Hanya drop kolom yang benar-benar ada
            if (Schema::hasColumn('energis', 'daya_listrik')) {
                $table->dropColumn('daya_listrik');
            }
            
            if (Schema::hasColumn('energis', 'pertalite')) {
                $table->dropColumn('pertalite');
            }
            
            if (Schema::hasColumn('energis', 'pertamax')) {
                $table->dropColumn('pertamax');
            }
            
            if (Schema::hasColumn('energis', 'solar')) {
                $table->dropColumn('solar');
            }
            
            if (Schema::hasColumn('energis', 'dexlite')) {
                $table->dropColumn('dexlite');
            }
            
            if (Schema::hasColumn('energis', 'pertamina_dex')) {
                $table->dropColumn('pertamina_dex');
            }
            
            if (Schema::hasColumn('energis', 'jenis_bbm')) {
                $table->dropColumn('jenis_bbm');
            }
            
            if (Schema::hasColumn('energis', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};