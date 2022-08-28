<?php

namespace App\Helpers;

class AppHelper
{
    public static function qr_code_format($mapel_id, $kelas_id)
    {
        $today = date('Y-m-d');
        return "$today-$mapel_id-$kelas_id";
    }
}
