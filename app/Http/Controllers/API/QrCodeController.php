<?php

namespace App\Http\Controllers\API;

use App\Helpers\AppHelper;
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
        $input = $request->all();

        $validator = Validator::make($input, [
            'mata_pelajaran_id' => 'required|integer',
            'kelas_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $generated_code = AppHelper::qr_code_format($input['mata_pelajaran_id'], $input['kelas_id']);
        $code = Hash::make($generated_code);
        $qr_code = base64_encode(QrCode::format('svg')->generate($code));

        return $this->handleResponse(new Resource([
            'qr_code' => $qr_code,
            'code' => $code,
        ]), 'Berhasil generate QR Code');
    }
}
