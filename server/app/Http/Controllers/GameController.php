<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Game;
use App\Mods\GameMaker;
use App\Models\AdminDepartment;

class GameController extends Controller
{
    // construct
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['index', 'show', 'indexAll', 'indexByDept', 'indexByTag']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($org_id)
    {
        $temp = AdminDepartment::where('admin_org_id', $org_id)->get();
        $deptArray = array();
        foreach ($temp as $t) {
            array_push($deptArray, strval($t->admin_dept_id));
        }
        if ($org_id == 1) {
            return response()->json(Game::leftJoin('sport_lists', 'sport_lists.sport_code', '=', 'games.sport_code')->where('archived', 0)->orderBy('event_start', 'desc')->paginate(10));
        } else {
            return response()->json(Game::leftJoin('sport_lists', 'sport_lists.sport_code', '=', 'games.sport_code')->where('archived', 0)->where(function ($query) use ($deptArray) {
                for ($i = 0; $i < count($deptArray); $i++) {
                    $query->orWhereJsonContains('host_list', [$deptArray[$i]]);
                }
            })->orderBy('event_start', 'desc')->paginate(10));
        }
    }
    /**
     * Display a listing of the resource. (No filtering)
     * 
     * @return \Illuminate\Http\Response
     */
    public function indexAll($org_id)
    {
        $temp = AdminDepartment::where('admin_org_id', $org_id)->get();
        $deptArray = array();
        foreach ($temp as $t) {
            array_push($deptArray, strval($t->admin_dept_id));
        }
        if ($org_id == 1) {
            return response()->json(Game::leftJoin('sport_lists', 'sport_lists.sport_code', '=', 'games.sport_code')->paginate(10));
        } else {
            return response()->json(Game::leftJoin('sport_lists', 'sport_lists.sport_code', '=', 'games.sport_code')->where(function ($query) use ($deptArray) {
                for ($i = 0; $i < count($deptArray); $i++) {
                    $query->orWhereJsonContains('host_list', [$deptArray[$i]]);
                }
            })->paginate(10));
        }
    }
    public function indexByDept($org_id, $dept_id, $all = NULL)
    {
        $query = Game::leftJoin('sport_lists', 'sport_lists.sport_code', '=', 'games.sport_code');
        if (is_null($all)) {
            $query->where('archived', 0);
        }
        if ($org_id == 1) {
            return response()->json($query->orderBy('event_start', 'desc')->paginate(10));
        } else {
            return response()->json($query->whereJsonContains('host_list', strval($dept_id))->orderBy('event_start', 'desc')->paginate(10));
        }
    }
    /**
     * Display a listing of the resource. (No filtering)
     * 
     * @return \Illuminate\Http\Response
     */
    public function indexByTag($org_id, $gameTag)
    {
        $temp = AdminDepartment::where('admin_org_id', $org_id)->get();
        $deptArray = array();
        foreach ($temp as $t) {
            array_push($deptArray, strval($t->admin_dept_id));
        }
        $query = Game::leftJoin('sport_lists', 'sport_lists.sport_code', '=', 'games.sport_code');
        if ($gameTag == 0) {
            return response()->json($query->where(function ($query) use ($deptArray) {
                for ($i = 0; $i < count($deptArray); $i++) {
                    $query->orWhereJsonContains('host_list', [$deptArray[$i]]);
                }
            })->where('archived', 0)->orderBy('event_start', 'desc')->paginate(10));
        } else {
            return response()->json($query->where(function ($query) use ($deptArray) {
                for ($i = 0; $i < count($deptArray); $i++) {
                    $query->orWhereJsonContains('host_list', [$deptArray[$i]]);
                }
            })->where('archived', 0)->orderBy('event_start', 'desc')->whereJsonContains('tags', $gameTag)->paginate(10));
        } 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $org_id)
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
            'sport_code' => 'required|size:4',
            'archived' => 'required|boolean',
            'options' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['create_dept_id'] = $user->admin_dept_id;
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
        Game::insert($temp);
        $game = Game::leftJoin('sport_lists', 'games.sport_code', '=', 'sport_lists.sport_code')->select('games.*', 'sport_lists.sport_code', 'sport_lists.module')->latest()->first();
        GameMaker::make($game->game_id, $game->sport_code, $game->module);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($org_id, $id)
    {
        return response()->json(Game::leftJoin('sport_lists', 'sport_lists.sport_code', '=', 'games.sport_code')->where('game_id', $id)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $org_id, $id)
    {
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
            'archived' => 'required|boolean',
            'options' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['updated_at'] = date("Y-m-d H:i:s");
        Game::where('game_id', $id)->update($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($org_id, $id)
    {
        $game = Game::leftJoin('sport_lists', 'games.sport_code', '=', 'sport_lists.sport_code')->select('games.*', 'sport_lists.sport_code', 'sport_lists.module')->where('game_id', $id)->first();
        GameMaker::reverse($game->game_id, $game->sport_code, $game->module);
        Game::where('game_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
