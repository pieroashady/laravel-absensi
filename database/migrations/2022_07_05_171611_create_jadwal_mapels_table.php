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
        Schema::create('jadwal_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwal_mapel');
    }
};
