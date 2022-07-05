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
        Schema::create('absen_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->dateTime('absen_masuk');
            $table->dateTime('absen_keluar');
            $table->string('keterangan');
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
        Schema::dropIfExists('absen_siswa');
    }
};
