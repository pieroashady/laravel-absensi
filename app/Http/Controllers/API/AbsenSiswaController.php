<?php

namespace App\Http\Controllers\API;

use App\Exports\AbsenSiswaExport;
use App\Http\Resources\Resource;
use App\Models\AbsenSiswa;
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
        $absen = AbsenSiswa::with(['siswa.kelas', 'absen' => function ($q) {
            $q->whereDate('tanggal', date('Y-m-d', strtotime('2022-07-30')));
        }])->whereHas('siswa', function ($q) use ($request) {
            if ($request['kelas_id']) {
                $q->where('kelas_id', '=', $request['kelas_id']);
            }
        })->filter();
        if ($request->get('q')) {
            $absen = $absen->search($request->get('q'));
        }
        $absen = $absen->paginate((int)$request->get('req_page', 15));
        return Resource::collection($absen);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'siswa_id' => 'required',
            'code' => 'required',
            'tipe' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $currentTime = date('G:i:s');
        $currentDate = date('Y-m-d');

        $input['jam_masuk'] = $currentTime;
        $input['tanggal'] = $currentDate;

        if (!Hash::check($input['tanggal'], $input['code'])) {
            return $this->handleError('Absen kadaluarsa atau tidak valid', [], 400);
        }

        $checkAbsen = AbsenSiswa::where('siswa_id', $input['siswa_id'])->whereDate('tanggal', '=', $currentDate)->first();

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
        $absen = AbsenSiswa::with(['siswa.kelas', 'absen'])->whereHas('siswa', function ($q) use ($request) {
            if ($request['kelas_id']) {
                $q->where('kelas_id', '=', $request['kelas_id']);
            }
        })->orderBy('created_at', 'desc');
        $absen->groupBy('siswa_id');
        $absen = $absen->filter()->first();

        if (!$absen) {
            return $this->handleError('Siswa tidak ditemukan');
        }

        $formattedDate = str_pad($date, 2, '0', STR_PAD_LEFT);
        $dateDay = date('l', strtotime("$year-$month-$formattedDate"));

        foreach ($absen->absen as $item) {
            $absenDate = date('j', strtotime($item->tanggal));

            if ($dateDay == "Saturday" || $dateDay == "Sunday") {
                $absenDesc = "-";
                break;
            };

            if ($date != (int) $absenDate) {
                $absenDesc = "A";
            } else {
                if ($item->keterangan) {
                    $absenDesc = $item->keterangan;
                    if ($item->keterangan == "Izin") {
                        $totalIzin += 1;
                    }
                    if ($item->keterangan == "Sakit") {
                        $totalSakit += 1;
                    }
                    break;
                }
                $absenDesc = "1";
                $totalHadir += 1;
                break;
            }
        }

        if ($absenDesc == 'A') {
            $totalAlpa += 1;
        }


        return $absen;
    }
}
