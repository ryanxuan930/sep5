<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Game;
use App\Mods\GameMaker;

class GameController extends Controller
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
        return response()->json(Game::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth('admin')->user();
        $validator = Validator::make($request->all(),[
            'game_name_ch' => 'required',
            'game_name_en' => 'nullable',
            'game_name_jp' => 'nullable',
            'game_info' => 'nullable',
            'host_list' => 'required',
            'event_start' => 'required|date',
            'selected' => 'required|boolean',
            'selected_list' => 'nullable',
            'use_reg' => 'required|boolean',
            'reg_url' => 'nullable',
            'use_manage' => 'required|boolean',
            'manage_url' => 'nullable',
            'use_site' => 'required|boolean',
            'site_url' => 'nullable',
            'tags' => 'nullable',
            'sport_code' => 'required|size:4'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['create_dept_id'] = $user->admin_dept_id;
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
        Game::insert($temp);
        $game = Game::latest('created_at')->leftJoin('sport_lists', 'games.sport_code', '=', 'sport_lists.sport_code')->first();
        GameMaker::make($game->game_id, $game->sport_code, $game->module);
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
        return response()->json(Game::where('game_id', $id)->first());
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
