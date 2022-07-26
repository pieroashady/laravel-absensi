<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\SiswaResource;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GuruController extends BaseController
{
    public function index(Request $request)
    {
        $guru = Guru::filter()->simplePaginate((int)$request->get('per_page', 15));
        return Resource::collection($guru);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nip' => 'required',
            'nama_guru' => 'required',
            'jenis_kelamin' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        try {
            DB::beginTransaction();
            $user = User::create([
                'username' => $input['nip'],
                'password' => bcrypt($input['tanggal_lahir']),
                'role' => 'guru'
            ]);
            $guru = $user->guru()->create($input);
            DB::commit();
            return $this->handleResponse(new Resource($guru), 'Berhasil menambahkan guru');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->handleError('Gagal menambahkan guru', [
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        $guru = Guru::find($id);
        if (is_null($guru)) {
            return $this->handleError('Data guru tidak ditemukan');
        }
        return $this->handleResponse(new Resource($guru), 'Berhasil menampilkan guru');
    }

    public function update(Request $request, Guru $guru)
    {
        $input = $request->all();
        $guru->update($input);
        return $this->handleResponse(new Resource($guru), 'Data guru berhasil diupdate');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return $this->handleResponse($guru, 'Data siswa berhasil dihapus');
    }
}
