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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->string('nama_siswa');
            $table->enum('jenis_kelamin', ["pria", "wanita"]);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('device_imei');
            $table->string('foto_siswa');
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
        Schema::dropIfExists('siswa');
    }
};
