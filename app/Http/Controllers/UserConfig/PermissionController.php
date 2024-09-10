<?php

namespace App\Http\Controllers\UserConfig;

use App\Http\Controllers\Controller;
use App\Models\WebSetup\SidebarNav;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
   /*  public function __construct()
    {
        $this->middleware('permission:create_permission', ['only' => ['create']]);
        $this->middleware('permission:view_permission', ['only' => ['index']]);
        $this->middleware('permission:update_permission', ['only' => ['edit']]);
        $this->middleware('permission:delete_permission', ['only' => ['destroy']]);
    } */

    public function index()
    {
        // $this->checkLogin();
        return view('UserConfig.permission.showPermission');
    }

    public function create()
    {
        // $this->checkLogin();
        return view('UserConfig.permission.createPermission');
    }

    public function edit($id)
    {
        // $this->checkLogin();
        $rowItem = Permission::where('id', $id)->first();
        return view('UserConfig.permission.editPermission', ['rowItem' => $rowItem]);
    }

    public function store(Request $request){
        try {
            if ($request['id']==""){
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                ]);
                if ($validator->fails()) {
                    return json_encode(array('statusCode' => 204,'statusMsg' => 'Validation Error.', 'errors' => $validator->errors()));
                }

                Permission::create([
                    'name'=>$request->name
                ]);

                return json_encode(array(
                    "statusCode" => 200,
                    "statusMsg" => "Data Added Successfully"
                ));

            }else{
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                ]);
                if ($validator->fails()) {
                    return json_encode(array('statusCode' => 204,'statusMsg' => 'Validation Error.', 'errors' => $validator->errors()));
                }

                $id = $request['id'];

                $permission = Permission::findById($id);
                $permission->update([
                    'name'=>$request->name
                ]);
                return json_encode(array(
                    "statusCode" => 200,
                    "statusMsg" => "Data Update Successfully"
                ));
            }

        } catch (\Exception $e) {

            return json_encode(array(
                "statusCode" => 400,
                "statusMsg" => $e->getMessage()
            ));;
        }
    }

    public function show($id){
        try {
            $singleDataShow = DB::table('permissions')->where('id', $id)->get();
            //$singleDataShow = Permission::findById($id)->get();
            return $singleDataShow;
        } catch (\Exception $e) {

            return json_encode(array(
                "statusCode" => 400,
                "statusMsg" => $e->getMessage()
            ));;
        }
    }

    public function destroy($id){
        try {
            $permission = Permission::findById($id);
            $permission->delete();
            return json_encode(array(
                "statusCode" => 200
            ));
        } catch (\Exception $e) {

            return json_encode(array(
                "statusCode" => 400,
                "statusMsg" => $e->getMessage()
            ));;
        }
    }

    public function getData(Request $request){

        $query = DB::table('permissions')
            ->select('id', 'name', 'guard_name', 'created_at', 'updated_at');

        if (isset($request->search['value'])) {
            $query->where('name', 'like', '%' . $request->search['value'] . '%');
        }

        // Ordering
        if ($request->has('order')) {
            $query->orderBy($request->columns[$request->order[0]['column']]['data'], $request->order[0]['dir']);
        }

        // Pagination
        $totalCount = $query->count();
        $filteredCount = $query->count();

        $data = $query->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ]);

    }
}
