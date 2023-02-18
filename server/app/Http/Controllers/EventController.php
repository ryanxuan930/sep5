<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Validator;
use App\Models\SportList;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    // construct
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['index', 'show', 'indexBySport']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Event::leftJoin('sport_lists', 'sport_lists.sport_id', '=', 'events.sport_id')->select('events.*', 'sport_lists.sport_name_ch', 'sport_lists.sport_name_en', 'sport_lists.sport_code')->get());
    }
    public function indexBySport($sportId)
    {
        return response()->json(Event::where('sport_id', $sportId)->get());
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
        $sportData = SportList::where('sport_id', $temp['sport_id'])->first();
        $count = $sportData->event_id_count + 1;
        $temp['event_code'] = $sportData->sport_code.str_pad($count, 4, '0', STR_PAD_LEFT);
        Event::insert($temp);
        SportList::where('sport_id', $temp['sport_id'])->update(['event_id_count' => $count]);
        return response()->json(['status'=>'A01']);
    }

    /**å
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Event::leftJoin('sport_lists', 'sport_lists.sport_id', '=', 'events.sport_id')->select('events.*', 'sport_lists.sport_name_ch', 'sport_lists.sport_name_en', 'sport_lists.sport_code')->where('events.event_id', $id)->first());
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
        Event::where('event_id', $id)->update($temp);
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
