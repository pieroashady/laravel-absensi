<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\SiswaResource;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KelasController extends BaseController
{
    public function index()
    {
        $kelas = Kelas::all();
        return $this->handleResponse(Resource::collection($kelas), 'Berhasil menampilkan data kelas');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nama_kelas' => 'required',
            'jurusan_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }
        $kelas = Kelas::create($input);
        return $this->handleResponse(new Resource($kelas), 'Berhasil menambahkan kelas');
    }

    public function show($id)
    {
        $kelas = Kelas::find($id);
        if (is_null($kelas)) {
            return $this->handleError('Data kelas tidak ditemukan');
        }
        return $this->handleResponse(new Resource($kelas), 'Berhasil menampilkan kelas');
    }

    public function update(Request $request, Kelas $kelas)
    {
        $input = $request->all();
        $kelas->update($input);
        return $this->handleResponse(new Resource($kelas), 'Data kelas berhasil diupdate');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return $this->handleResponse($kelas, 'Data siswa berhasil dihapus');
    }
}
