<?php

namespace App\Exports;

use App\Models\AbsenSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AbsenSiswaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private $request;

    // use constructor to handle dependency injection
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $requestParams = $this->request;
        Log::debug('An informational message.');


        $absen = AbsenSiswa::with(['siswa.kelas'])->whereHas('siswa', function ($q) use ($requestParams) {
            if ($requestParams['kelas_id']) {
                $q->where('kelas_id', '=', $requestParams['kelas_id']);
            }
        })->orderBy('created_at', 'desc');

        if ($requestParams['type'] == 'bulanan') {
            $month = $requestParams->month;
            $year = $requestParams->year;

            $absen->with(['absen' => function ($q) use ($month, $year) {
                $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            }]);

            $absen->groupBy('siswa_id');
        }

        if ($requestParams['type'] == 'harian') {
            Log::debug('An informational message.');
            $date = $requestParams->date;

            $absen->where('tanggal', date('Y-m-d', strtotime($date)));
        }

        $absen = $absen->filter()->get();

        return $absen;
    }

    private function monthlyHeadings()
    {
        $dcount = cal_days_in_month(CAL_GREGORIAN, (int) $this->request->month, (int) $this->request->year);

        $currentHeader = ["NIS", "Nama"];

        for ($i = 0; $i < $dcount; $i++) {
            array_push($currentHeader, $i + 1);
        };

        array_push($currentHeader, "Izin", "Sakit", "Alpa");

        return $currentHeader;
    }

    public function headings(): array
    {
        $dailyHeaders = ["NIS", "Nama", "Kelas", "Tanggal", "Jam Masuk", "Jam Keluar", "Keterangan"];

        $requestParams = $this->request;

        if ($requestParams['type'] == 'harian') {
            return $dailyHeaders;
        }

        return $this->monthlyHeadings();
    }

    public function map($absen_siswa): array
    {
        $dailyMap = [
            $absen_siswa->siswa->nis,
            $absen_siswa->siswa->nama_siswa,
            $absen_siswa->siswa->kelas->nama_kelas,
            $absen_siswa->tanggal,
            $absen_siswa->jam_masuk,
            $absen_siswa->jam_keluar,
            $absen_siswa->keterangan,
        ];

        $requestParams = $this->request;

        if ($requestParams['type'] == 'harian') {
            return $dailyMap;
        }

        $month = $requestParams->month;
        $year = $requestParams->year;

        $requestMonth = ltrim($month, '0');

        $dcount = cal_days_in_month(CAL_GREGORIAN, (int) $requestMonth, (int) $year);

        $mapping = [
            $absen_siswa->siswa->nis,
            $absen_siswa->siswa->nama_siswa,
        ];

        $currentDate = date('j');
        $currentMonth = date('n');
        $absenDesc = "-";
        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;

        for ($i = 0; $i < $dcount; $i++) {
            $date = $i + 1;

            if ($requestMonth == $currentMonth) {
                if ($date >= $currentDate) {
                    array_push($mapping, null);
                    continue;
                }
            }

            $formattedDate = str_pad($date, 2, '0', STR_PAD_LEFT);
            $dateDay = date('l', strtotime("$year-$month-$formattedDate"));

            foreach ($absen_siswa->absen as $item) {
                $absenDate = date('j', strtotime($item->tanggal));

                if ($dateDay == "Saturday" || $dateDay == "Sunday") {
                    $absenDesc = "-";
                    break;
                };

                if ($date != (int) $absenDate) {
                    $absenDesc = "A";
                    $totalAlpa += 1;
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

            array_push($mapping, $absenDesc);
        };

        array_push($mapping, (string) $totalIzin, (string) $totalSakit, (string) $totalAlpa);

        return $mapping;
    }
}
