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
    public function index(Request $request)
    {
        $jadwalMapel = JadwalMapel::with(['guru', 'kelas', 'mata_pelajaran']);
        if ($request->get('q')) {
            $jadwalMapel = $jadwalMapel->search($request->get('q'));
        }
        $jadwalMapel = $jadwalMapel->simplePaginate((int)$request->get('per_page', 15));
        return Resource::collection($jadwalMapel);
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
        $checkGuruMapel = JadwalMapel::where([
            ['guru_id', '=', $input['guru_id']],
            ['kelas_id', '=', $input['kelas_id']],
            ['mata_pelajaran_id', '=', $input['mata_pelajaran_id']]
        ])->first();
        if ($checkGuruMapel) {
            return $this->handleError('Data sudah ada', [], 400);
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

    public function update(Request $request, JadwalMapel $jadwal_mapel)
    {
        $input = $request->all();
        $jadwal_mapel->update($input);
        return $this->handleResponse(new Resource($jadwal_mapel), 'Data jadwal mapel berhasil diupdate');
    }

    public function destroy(JadwalMapel $jadwal_mapel)
    {
        $jadwal_mapel->delete();
        return $this->handleResponse($jadwal_mapel, 'Data jadwal mapel berhasil dihapus');
    }
}
