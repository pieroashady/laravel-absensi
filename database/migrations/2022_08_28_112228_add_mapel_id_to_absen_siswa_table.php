<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('absen_siswa', 'mata_pelajaran_id')) {
            Schema::table('absen_siswa', function (Blueprint $table) {
                $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajaran');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('absen_siswa', 'mata_pelajaran_id')) {
            Schema::table('absen_siswa', function (Blueprint $table) {
                $table->dropColumn('mata_pelajaran_id');
            });
        }
    }
};
