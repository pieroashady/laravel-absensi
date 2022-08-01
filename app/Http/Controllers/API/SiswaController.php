<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\SiswaResource;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SiswaController extends BaseController
{
    public function index(Request $request)
    {
        $siswa = Siswa::with(['kelas'])->filter();
        if ($request->get('q')) {
            $siswa = $siswa->search($request->get('q'));
        }
        $siswa = $siswa->simplePaginate((int)$request->get('per_page', 15));
        return Resource::collection($siswa);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'kelas_id' => 'required',
            'nis' => 'required',
            'nama_siswa' => 'required',
            'jenis_kelamin' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'phone_number' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors(), [], 400);
        }

        try {
            DB::beginTransaction();
            $user = User::create([
                'username' => $input['nis'],
                'password' => bcrypt($input['tanggal_lahir']),
                'role' => 'siswa'
            ]);
            $siswa = $user->siswa()->create($input);
            DB::commit();
            return $this->handleResponse(new Resource($siswa), 'Berhasil menambahkan siswa');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->handleError('Gagal menambahkan siswa', [], 400);
        }
    }

    public function show($id)
    {
        $siswa = Siswa::find($id);
        if (is_null($siswa)) {
            return $this->handleError('Data siswa tidak ditemukan');
        }
        return $this->handleResponse(new Resource($siswa), 'Berhasil menampilkan siswa');
    }

    public function update(Request $request, Siswa $siswa)
    {
        $input = $request->all();
        $siswa->update($input);
        return $this->handleResponse(new Resource($siswa), 'Data siswa berhasil diupdate');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return $this->handleResponse($siswa, 'Data siswa berhasil dihapus');
    }
}
