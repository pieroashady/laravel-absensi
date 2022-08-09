<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\SiswaResource;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TahunAjaranController extends BaseController
{
    public function index()
    {
        $tahun = TahunAjaran::all();
        return Resource::collection($tahun);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'mulai_tahun_ajaran' => 'required',
            'akhir_tahun_ajaran' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }
        $tahun = TahunAjaran::create($input);
        return $this->handleResponse(new Resource($tahun), 'Berhasil menambahkan tahun');
    }

    public function show($id)
    {
        $tahun = TahunAjaran::find($id);
        if (is_null($tahun)) {
            return $this->handleError('Data tahun tidak ditemukan');
        }
        return $this->handleResponse(new Resource($tahun), 'Berhasil menampilkan tahun');
    }

    public function update(Request $request, TahunAjaran $tahun_ajaran)
    {
        $input = $request->all();
        $tahun_ajaran->update($input);
        return $this->handleResponse(new Resource($tahun_ajaran), 'Data tahun berhasil diupdate');
    }

    public function destroy(TahunAjaran $tahun_ajaran)
    {
        $tahun_ajaran->delete();
        return $this->handleResponse($tahun_ajaran, 'Data tahun berhasil dihapus');
    }
}
