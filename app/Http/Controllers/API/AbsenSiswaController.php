<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\SiswaResource;
use App\Models\AbsenSiswa;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

date_default_timezone_set('Asia/Jakarta');

class AbsenSiswaController extends BaseController
{
    public function index(Request $request)
    {
        $absen = AbsenSiswa::with(['siswa.kelas'])->filter();
        if ($request->get('q')) {
            $absen = $absen->search($request->get('q'));
        }
        $absen = $absen->simplePaginate((int)$request->get('req_page', 15));
        return Resource::collection($absen);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'siswa_id' => 'required',
            'code' => 'required',
            'tipe' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $currentTime = date('G:i:s');
        $currentDate = date('Y-m-d');

        if ($input['tipe'] == 'keluar') {
            $checkAbsen = AbsenSiswa::whereDate('tanggal', '=', $currentDate)->first();
            if (!$checkAbsen) {
                return $this->handleError('Anda belum melakukan absen masuk', [], 400);
            }
            if ($checkAbsen->jam_keluar) {
                return $this->handleError('Anda telah melakukan absen keluar hari ini', [], 400);
            }
            $checkAbsen->jam_keluar = $currentTime;
            $checkAbsen->save();
            return $this->handleResponse(new Resource($checkAbsen), 'Berhasil absen keluar');
        }

        $checkAbsen = AbsenSiswa::whereDate('tanggal', '=', $currentDate)->first();
        if ($checkAbsen) {
            return $this->handleError('Anda telah melakukan absen masuk hari ini', [], 400);
        }

        $input['jam_masuk'] = $currentTime;
        $input['tanggal'] = $currentDate;

        if (!Hash::check($input['tanggal'], $input['code'])) {
            return $this->handleError('Absen kadaluarsa atau tidak valid', [], 400);
        }

        $absen = AbsenSiswa::create($input);
        return $this->handleResponse(new Resource($absen), 'Berhasil absen masuk');
    }

    public function show($id)
    {
        $absen = AbsenSiswa::find($id);
        if (is_null($absen)) {
            return $this->handleError('Data absen tidak ditemukan');
        }
        return $this->handleResponse(new Resource($absen), 'Berhasil menampilkan absen');
    }

    public function update(Request $request, AbsenSiswa $absen)
    {
        $input = $request->all();
        $absen->update($input);
        return $this->handleResponse(new Resource($absen), 'Data absen berhasil diupdate');
    }

    public function destroy(AbsenSiswa $absen)
    {
        $absen->delete();
        return $this->handleResponse($absen, 'Data absen berhasil dihapus');
    }
}
