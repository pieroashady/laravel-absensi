<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\SiswaResource;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MataPelajaranController extends BaseController
{
    public function index()
    {
        $mapel = MataPelajaran::all();
        return $this->handleResponse(Resource::collection($mapel), 'Berhasil menampilkan data mapel');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nama_mapel' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }
        $mapel = MataPelajaran::create($input);
        return $this->handleResponse(new Resource($mapel), 'Berhasil menambahkan mapel');
    }

    public function show($id)
    {
        $mapel = MataPelajaran::find($id);
        if (is_null($mapel)) {
            return $this->handleError('Data mapel tidak ditemukan');
        }
        return $this->handleResponse(new Resource($mapel), 'Berhasil menampilkan mapel');
    }

    public function update(Request $request, MataPelajaran $mapel)
    {
        $input = $request->all();
        $mapel->update($input);
        return $this->handleResponse(new Resource($mapel), 'Data mapel berhasil diupdate');
    }

    public function destroy(MataPelajaran $mapel)
    {
        $mapel->delete();
        return $this->handleResponse($mapel, 'Data siswa berhasil dihapus');
    }
}
