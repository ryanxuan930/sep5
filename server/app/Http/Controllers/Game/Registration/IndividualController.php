<?php

namespace App\Http\Controllers\Game\Registration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Models\SportList;
use App\Mods\GameFunctions;

class IndividualController extends Controller
{
    private $tableName = 'individuals';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($sportCode, $gameId)
    {
        return response()->json(DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)
        ->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_divisions.division_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id')
        ->leftJoin('users', 'users.u_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.u_id')
        ->leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')
        ->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')
        ->leftJoin('events', 'events.event_code', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code')
        ->select($sportCode.'_'.$gameId.'_'.$this->tableName.'.*', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', 'events.event_ch', 'events.event_en', 'events.event_jp', 'events.event_abbr', $sportCode.'_'.$gameId.'_divisions.*')
        ->get());
    }
    public function indexByUser($sportCode, $gameId)
    {
        if (is_null($user = auth('user')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $permission = $user->permission;
        $query = DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)
        ->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_divisions.division_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id')
        ->leftJoin('users', 'users.u_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.u_id')
        ->leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')
        ->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')
        ->leftJoin('events', 'events.event_code', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code')
        ->select($sportCode.'_'.$gameId.'_'.$this->tableName.'.*', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.org_code', 'users.dept_id', 'users.sex', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', 'events.event_ch', 'events.event_en', 'events.event_jp', 'events.event_abbr', $sportCode.'_'.$gameId.'_divisions.*');
        if ($permission == 0){
            return response()->json($query->where('users.u_id', $user->u_id)->get());
        } else if ($permission == 1) {
            return response()->json($query->where('users.dept_id', $user->dept_id)->get());
        } else if ($permission == 2) {
            return response()->json($query->where('users.org_code', $user->org_code)->get());
        } else {
            return response()->json([]);
        }
    }
    public function indexByEvent($sportCode, $gameId, $divisionId, $eventCode)
    {
        if (is_null($user = auth('user')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $permission = $user->permission;
        $query = DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)
        ->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_divisions.division_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id')
        ->leftJoin('users', 'users.u_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.u_id')
        ->leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')
        ->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')
        ->leftJoin('events', 'events.event_code', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code')
        ->select($sportCode.'_'.$gameId.'_'.$this->tableName.'.*', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.org_code', 'users.dept_id', 'users.sex', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', 'events.event_ch', 'events.event_en', 'events.event_jp', 'events.event_abbr', $sportCode.'_'.$gameId.'_divisions.*');
        return response()->json($query->where($sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id', $divisionId)->where($sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code', $eventCode));
    }
    public function indexByCount($sportCode, $gameId, $unit)
    {
        if (is_null($user = auth('user')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        $query = DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->leftJoin('users', 'users.u_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.u_id');
        $table = DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->leftJoin('users', 'users.u_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.u_id');
        if ($unit == 2) {
            $query->where('users.org_code', $user->org_code);
            $table->where('users.org_code', $user->org_code);
        } else if ($unit == 1) {
            $query->where('users.dept_id', $user->dept_id);
            $table->where('users.dept_id', $user->dept_id);
        } else {
            $query->where('users.u_id', $user->u_id);
            $table->where('users.u_id', $user->u_id);
        }
        $result = array();
        $result['event'] = $query->select($sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code', DB::raw('count(*) as total'))->groupBy($sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code')->get();
        $result['athlete'] = $table->select($sportCode.'_'.$gameId.'_'.$this->tableName.'.u_id', DB::raw('count(*) as total'))->groupBy($sportCode.'_'.$gameId.'_'.$this->tableName.'.u_id')->get();
        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $sportCode, $gameId)
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        // get sport module
        $sportData = SportList::where('sport_code', $sportCode)->first();
        $validationArray = [
            'u_id' => 'required|integer',
            'division_id' => 'required|integer',
            'event_code' => 'required|size:8',
        ];
        if ($sportData->module == 'ln' || $sportData->module == 'rd') {
            $validationArray['ref_result'] = 'required';
        } else {
            unset($request->all()['ref_result']);
        }
        $validator = Validator::make($request->all(),$validationArray);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->insert($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($sportCode, $gameId, $id)
    {
        return response()->json(DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)
        ->leftJoin($sportCode.'_'.$gameId.'_divisions', $sportCode.'_'.$gameId.'_divisions.division_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.division_id')
        ->leftJoin('users', 'users.u_id', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.u_id')
        ->leftJoin('organizations', 'users.org_code', '=', 'organizations.org_code')
        ->leftJoin('departments', 'users.dept_id', '=', 'departments.dept_id')
        ->leftJoin('events', 'events.event_code', '=', $sportCode.'_'.$gameId.'_'.$this->tableName.'.event_code')
        ->select($sportCode.'_'.$gameId.'_'.$this->tableName.'.*', 'users.first_name_ch', 'users.last_name_ch', 'users.first_name_en', 'users.last_name_en', 'users.org_code', 'users.dept_id', 'users.sex', 'users.num_in_dept', 'organizations.org_name_full_ch', 'organizations.org_name_ch', 'organizations.org_name_full_en', 'organizations.org_name_en', 'departments.dept_name_ch', 'departments.dept_name_en', 'events.event_ch', 'events.event_en', 'events.event_jp', 'events.event_abbr', $sportCode.'_'.$gameId.'_divisions.*')
        ->where($sportCode.'_'.$gameId.'_'.$this->tableName.'.ind_id', $id)
        ->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $sportCode, $gameId, $id)
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        // get sport module
        $sportData = SportList::where('sport_code', $sportCode)->first();
        $validationArray = [
            'u_id' => 'required|integer',
            'division_id' => 'required|integer',
            'event_code' => 'required|size:8',
        ];
        if ($sportData->module == 'ln' || $sportData->module == 'rd') {
            $validationArray['ref_result'] = 'required';
        }
        $validator = Validator::make($request->all(),$validationArray);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $temp = $request->all();
        DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->where('ind_id', $id)->update($temp);
        return response()->json(['status'=>'A01']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($sportCode, $gameId, $id)
    {
        if (is_null(auth('user')->user()) && is_null(auth('admin')->user())) {
            return response()->json(['status'=>'E04', 'message'=>'unauthenticated']);
        }
        DB::table($sportCode.'_'.$gameId.'_'.$this->tableName)->where('ind_id', $id)->delete();
        return response()->json(['status'=>'A01']);
    }
}
