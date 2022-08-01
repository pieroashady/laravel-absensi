<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\Resource;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
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

    public function update(Request $request, MataPelajaran $mata_pelajaran)
    {
        $input = $request->all();
        $mata_pelajaran->update($input);
        return $this->handleResponse(new Resource($mata_pelajaran), 'Data mata pelajaran berhasil diupdate');
    }

    public function destroy(MataPelajaran $mata_pelajaran)
    {
        $mata_pelajaran->delete();
        return $this->handleResponse($mata_pelajaran, 'Data mata pelajaran berhasil dihapus');
    }
}
