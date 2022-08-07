<?php

namespace App\Exports;

use App\Models\AbsenSiswa;
use Illuminate\Http\Request;
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
        $absen = AbsenSiswa::with(['siswa.kelas', 'absen'])->whereHas('siswa', function ($q) use ($requestParams) {
            if ($requestParams['kelas_id']) {
                $q->where('kelas_id', '=', $requestParams['kelas_id']);
            }
        })->groupBy('siswa_id')->orderBy('created_at', 'desc')->filter()->get();
        return $absen;
    }

    public function headings(): array
    {
        $dcount = cal_days_in_month(CAL_GREGORIAN, (int) $this->request->month, (int) $this->request->year);

        $currentHeader = ["NIS", "Nama"];

        for ($i = 0; $i < $dcount; $i++) {
            array_push($currentHeader, $i + 1);
        };

        array_push($currentHeader, "Hadir", "Izin", "Sakit", "Alpa");

        return $currentHeader;
    }

    public function map($absen_siswa): array
    {
        $dcount = cal_days_in_month(CAL_GREGORIAN, (int) $this->request->month, (int) $this->request->year);

        $mapping = [
            $absen_siswa->siswa->nis,
            $absen_siswa->siswa->nama_siswa,
        ];

        $currentDate = date('j');
        $absenDesc = "-";
        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalAlpa = 0;

        for ($i = 0; $i < $dcount; $i++) {
            $date = $i + 1;

            if ($date >= $currentDate) {
                array_push($mapping, null);
                continue;
            }

            $formattedDate = str_pad($date, 2, '0', STR_PAD_LEFT);
            $dateDay = date('l', strtotime("2022-08-$formattedDate"));

            foreach ($absen_siswa->absen as $item) {
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

            array_push($mapping, $absenDesc);
        };

        array_push($mapping, (string) $totalHadir, (string) $totalIzin, (string) $totalSakit, (string) $totalAlpa);

        return $mapping;
    }
}
