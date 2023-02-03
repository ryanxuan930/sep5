<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Validator;
use App\Models\Bulletin;

class BulletinController extends Controller
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
    public function index($org_id)
    {
        if ($org_id == 1) {
            return response()->json(Bulletin::leftJoin('games', 'games.game_id', '=', 'bulletins.related_game')
            ->leftJoin('admin_departments', 'admin_departments.admin_dept_id', '=', 'bulletins.related_admin_dept')->paginate(10));
        } else {
            return response()->json(Bulletin::leftJoin('games', 'games.game_id', '=', 'bulletins.related_game')->leftJoin('admin_departments', 'admin_departments.admin_dept_id', '=', 'bulletins.related_admin_dept')->where('admin_departments.admin_org_id', $org_id)->paginate(10));
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
            'post_date' => 'required|date',
            'category' => 'required|integer',
            'pinned' => 'required|boolean',
            'multilingual' => 'required|boolean',
            'related_game' => 'required|integer',
            'title_ch' => 'required',
            'title_en' => 'nullable',
            'content_ch' => 'nullable',
            'content_en' => 'nullable',
            'links' => 'nullable',
            'release' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['post_by'] = $user->admin_id;
        $temp['related_admin_dept'] = $user->admin_dept_id;
        $temp['created_at'] = date("Y-m-d H:i:s");
        $temp['updated_at'] = date("Y-m-d H:i:s");
        Bulletin::insert($temp);
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
        return response()->json(Bulletin::leftJoin('games', 'games.game_id', '=', 'bulletins.related_game')->where('bulletin_id', $id)->first());
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
            'post_date' => 'required|date',
            'category' => 'required|integer',
            'pinned' => 'required|boolean',
            'multilingual' => 'required|boolean',
            'related_game' => 'required|integer',
            'title_ch' => 'required',
            'title_en' => 'nullable',
            'content_ch' => 'nullable',
            'content_en' => 'nullable',
            'links' => 'nullable',
            'release' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        $temp['updated_at'] = date("Y-m-d H:i:s");
        Bulletin::where('bulletin_id', $id)->update($temp);
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
        Bulletin::where('bulletin_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
