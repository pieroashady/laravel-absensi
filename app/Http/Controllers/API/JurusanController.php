<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\SiswaResource;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JurusanController extends BaseController
{
    public function index(Request $request)
    {
        $jurusan = Jurusan::filter()->simplePaginate((int)$request->get('per_page', 15));
        return Resource::collection($jurusan);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nama_jurusan' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }
        $jurusan = Jurusan::create($input);
        return $this->handleResponse(new Resource($jurusan), 'Berhasil menambahkan jurusan');
    }

    public function show($id)
    {
        $jurusan = Jurusan::find($id);
        if (is_null($jurusan)) {
            return $this->handleError('Data jurusan tidak ditemukan');
        }
        return $this->handleResponse(new Resource($jurusan), 'Berhasil menampilkan jurusan');
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $input = $request->all();
        $jurusan->update($input);
        return $this->handleResponse(new Resource($jurusan), 'Data jurusan berhasil diupdate');
    }

    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return $this->handleResponse($jurusan, 'Data siswa berhasil dihapus');
    }
}
