<?php

namespace App\Http\Controllers\API;

use App\Exports\AbsenSiswaExport;
use App\Helpers\AppHelper;
use App\Http\Resources\Resource;
use App\Models\AbsenSiswa;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

date_default_timezone_set('Asia/Jakarta');

class AbsenSiswaController extends BaseController
{
    public function index(Request $request)
    {
        $absen = AbsenSiswa::with(['siswa.kelas'])->whereHas('siswa', function ($q) use ($request) {
            if ($request['kelas_id']) {
                $q->where('kelas_id', '=', $request['kelas_id']);
            }
        })->filter();
        if ($request->get('q')) {
            $absen = $absen->search($request->get('q'));
        }
        $absen = $absen->paginate((int)$request->get('per_page', 15));
        return Resource::collection($absen);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'siswa_id' => 'required',
            'code' => 'required',
            'tipe' => 'required',
            'mata_pelajaran_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $siswa = Siswa::find($input['siswa_id']);

        if (!$siswa) {
            return $this->handleError('Id siswa tidak ditemukan', [], 404);
        }

        $currentTime = date('G:i:s');
        $currentDate = date('Y-m-d');

        $input['jam_masuk'] = $currentTime;
        $input['tanggal'] = $currentDate;

        $generated_code = AppHelper::qr_code_format($input['mata_pelajaran_id'], $siswa['kelas_id']);

        if (!Hash::check($generated_code, $input['code'])) {
            return $this->handleError('Absen kadaluarsa atau tidak valid', [], 400);
        }

        $checkAbsen = AbsenSiswa::where('siswa_id', $input['siswa_id'])
            ->where('mata_pelajaran_id', $input['mata_pelajaran_id'])
            ->whereDate('tanggal', '=', $currentDate)->first();

        if ($input['tipe'] == 'keluar') {
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

        if ($checkAbsen) {
            return $this->handleError('Anda telah melakukan absen masuk hari ini', [], 400);
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

    public function update(Request $request, AbsenSiswa $absen_siswa)
    {
        $input = $request->all();
        $absen_siswa->update($input);
        return $this->handleResponse(new Resource($absen_siswa), 'Data absen berhasil diupdate');
    }

    public function destroy(AbsenSiswa $absen_siswa)
    {
        $absen_siswa->delete();
        return $this->handleResponse($absen_siswa, 'Data absen berhasil dihapus');
    }

    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required',
            'date' => ['required_if:type,==,harian', 'date'],
            'month' => 'required_if:type,==,bulanan',
            'year' => 'required_if:type,==,bulanan',
            'type' => ['required', Rule::in(['harian', 'bulanan'])],
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }
        return Excel::download(new AbsenSiswaExport($request), 'absen.xlsx');
    }

    public function rekap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'siswa_id' => 'required',
            'mata_pelajaran_id' => 'required'
        ]);

        $siswa = Siswa::find($request->siswa_id);
        if (is_null($siswa)) {
            return $this->handleError('Siswa tidak ditemukan');
        }

        $totalAlpa = AbsenSiswa::where('siswa_id', $request->siswa_id)->where('mata_pelajaran_id', $request->mata_pelajaran_id)->where('keterangan', 'Alpa')->count();
        $totalIzin = AbsenSiswa::where('siswa_id', $request->siswa_id)->where('mata_pelajaran_id', $request->mata_pelajaran_id)->where('keterangan', 'Izin')->count();
        $totalSakit = AbsenSiswa::where('siswa_id', $request->siswa_id)->where('mata_pelajaran_id', $request->mata_pelajaran_id)->where('keterangan', 'Sakit')->count();

        return $this->handleResponse([
            "total_alpa" => $totalAlpa,
            "total_izin" => $totalIzin,
            "total_sakit" => $totalSakit
        ], "Berhasil mendapatkan data");
    }
}
