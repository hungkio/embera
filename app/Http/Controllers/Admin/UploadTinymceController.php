<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadTinymceController
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'file' => ['mimes:jpeg,jpg,png', 'required', 'max:2048'],
            ],
            [
                'file.mimes' => __('Tệp tải lên không hợp lệ !'),
                'file.max' => __('Tệp quá lớn !'),
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first('file'),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (config('filesystems.default') == 'local') {
            $file = $request->file('file')->store('uploads', 'public');
        } else {
            $file = $request->file('file')->storePublicly('uploads');
        }

        return response()->json([
            'location' => Storage::url($file),
        ]);
    }
}
