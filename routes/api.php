<?php

use App\Http\Controllers\API\AbsenSiswaController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GuruController;
use App\Http\Controllers\API\JadwalMapelController;
use App\Http\Controllers\API\JurusanController;
use App\Http\Controllers\API\KelasController;
use App\Http\Controllers\API\MataPelajaranController;
use App\Http\Controllers\API\QrCodeController;
use App\Http\Controllers\API\SiswaController;
use App\Http\Controllers\API\TahunAjaranController;
use App\Http\Controllers\API\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get("/user", [AuthController::class, 'profile']);
    Route::get("/refresh", [AuthController::class, 'refresh']);
});

Route::resource('jurusan', JurusanController::class);
Route::resource('kelas', KelasController::class);
Route::resource('guru', GuruController::class);
Route::resource('siswa', SiswaController::class);
Route::resource('absen-siswa', AbsenSiswaController::class);
Route::resource('mata-pelajaran', MataPelajaranController::class);
Route::resource('jadwal-mapel', JadwalMapelController::class);
Route::resource('tahun-ajaran', TahunAjaranController::class);
Route::get('absen-siswa-export', [AbsenSiswaController::class, 'export']);
Route::get('rekap-absen-siswa', [AbsenSiswaController::class, 'rekap']);

Route::get('qr', [QrCodeController::class, 'index']);
Route::post('upload', [UploadController::class, 'index']);

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
