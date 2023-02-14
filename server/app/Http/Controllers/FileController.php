<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    function upload(Request $request)
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        if ($request->hasFile('file')) {
            $fileName = $request->file_name;
            $fileFormat = $request->file_format;
            if ($fileName) {
                Storage::disk('public')->delete($fileName.'.'.$fileFormat);
            } 
            $path = $request->file('file')->store($fileName, 'public');
            return response()->json(['status'=>'A05']);
        } else {
            return response()->json(['status'=>'U08', 'message' => '上傳錯誤']);
        }
    }
}
