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
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->string('nis')->unique();
            $table->string('nama_siswa');
            $table->string('phone_number')->nullable();
            $table->enum('jenis_kelamin', ["0", "1"]);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('foto_siswa')->nullable();
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
