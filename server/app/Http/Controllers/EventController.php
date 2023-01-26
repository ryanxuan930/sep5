<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    // construct
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['index', 'show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Event::all());
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
            'sport_id' => 'required',
            'event_code' => 'required|size:8',
            'event_ch' => 'required',
            'event_en' => 'required',
            'event_jp' => 'nullable',
            'event_abbr' => 'required',
            'unit' => 'required|size:1',
            'display' => 'required|boolean',
            'built_in' => 'required|boolean',
            'multiple' => 'required|boolean',
            'combined' => 'required|boolean',
            'combined_list' => 'nullable',
            'player_num' => 'required|integer',
            'created_by_dept' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
        GameTag::insert($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Event::where('event_id', $id)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'sport_id' => 'required',
            'event_code' => 'required|size:8',
            'event_ch' => 'required',
            'event_en' => 'required',
            'event_jp' => 'nullable',
            'event_abbr' => 'required',
            'unit' => 'required|size:1',
            'display' => 'required|boolean',
            'built_in' => 'required|boolean',
            'multiple' => 'required|boolean',
            'combined' => 'required|boolean',
            'combined_list' => 'nullable',
            'player_num' => 'required|integer',
            'created_by_dept' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['updated_at'] = date("Y-m-d H:i:s");
        GameTag::where('event_id', $id)->update($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Event::where('event_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
