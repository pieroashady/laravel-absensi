<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Http\Resources\Resource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

date_default_timezone_set('Asia/Jakarta');

class QrCodeController extends BaseController
{
    public function index(Request $request)
    {
        $now = date('Y-m-d');
        $code = Hash::make($now);

        $qrCode = base64_encode(QrCode::format('svg')->generate($code));
        return $this->handleResponse(new Resource([
            'qr_code' => $qrCode,
            'code' => $code,
        ]), 'Berhasil generate QR Code');
    }
}
