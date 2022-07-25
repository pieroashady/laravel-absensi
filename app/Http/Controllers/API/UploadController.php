<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UploadController extends BaseController
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:1048',
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors(), [], 400);
        }

        $name = $request->file('image')->getClientOriginalName();
        $request->file('image')->store('public/images');
        $url = $request->file('image')->hashName();

        return response()->json([
            'name' => $name,
            'url' => $url
        ]);
    }
}
