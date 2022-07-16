<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\SiswaResource;
use App\Models\JadwalMapel;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JadwalMapelController extends BaseController
{
    public function index()
    {
        $jadwalMapel = JadwalMapel::all();
        return $this->handleResponse(Resource::collection($jadwalMapel), 'Berhasil menampilkan data jadwal mapel');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'guru_id' => 'required',
            'kelas_id' => 'required',
            'mata_pelajaran_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }
        $jadwalMapel = JadwalMapel::create($input);
        return $this->handleResponse(new Resource($jadwalMapel), 'Berhasil menambahkan jadwal mapel');
    }

    public function show($id)
    {
        $jadwalMapel = JadwalMapel::find($id);
        if (is_null($jadwalMapel)) {
            return $this->handleError('Data jadwal mapel tidak ditemukan');
        }
        return $this->handleResponse(new Resource($jadwalMapel), 'Berhasil menampilkan jadwal mapel');
    }

    public function update(Request $request, JadwalMapel $jadwalMapel)
    {
        $input = $request->all();
        $jadwalMapel->update($input);
        return $this->handleResponse(new Resource($jadwalMapel), 'Data jadwal mapel berhasil diupdate');
    }

    public function destroy(JadwalMapel $jadwalMapel)
    {
        $jadwalMapel->delete();
        return $this->handleResponse($jadwalMapel, 'Data jadwal mapel berhasil dihapus');
    }
}
