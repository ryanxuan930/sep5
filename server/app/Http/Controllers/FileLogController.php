<?php

namespace App\Http\Controllers;

use App\Models\FileLog;
use Validator;
use Illuminate\Http\Request;

class FileLogController extends Controller
{
    // construct
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('admin')->user();
        if ($user->admin_org_id == 1) {
            return response()->json(FileLog::all());
        } else {
            return response()->json(FileLog::where('admin_org_id', $user->admin_org_id)->get());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'admin_org_id' => 'required|integer',
            'file_name' => 'required',
            'format' => 'required',
            'path' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
        FileLog::insert($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FileLog  $fileLog
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(FileLog::where('file_id', $id)->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FileLog  $fileLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FileLog  $fileLog
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        FileLog::where('file_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
