<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $auth = Auth::user();
            $auth['token'] =  $auth->createToken('LaravelSanctumAuth')->plainTextToken;

            if ($request->imei_device && !$auth->imei_device) {
                $auth->imei_device = $request->imei_device;
                $auth->save();
            };

            return $this->handleResponse($auth->load('siswa'), 'User logged-in!');
        } else {
            return $this->handleError('Akun tidak ditemukan.', ['error' => 'Unauthorized']);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
            'role' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        try {
            $user = User::create($input);
            $user['token'] =  $user->createToken($user->username)->plainTextToken;
            return $this->handleResponse($user, 'User successfully registered!');
        } catch (Exception $ex) {
            return $this->handleError('Akun sudah terdaftar.', ['error' => 'Akun sudah terdaftar']);
        }
    }

    public function profile(Request $request)
    {
        return response()->json($request->user()->load(['siswa.kelas']));
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        return response()->json(['token' => $user->createToken($user->username)->plainTextToken]);
    }
}
