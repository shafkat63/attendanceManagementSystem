<?php

namespace App\Http\Controllers\UserConfig;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserConfig\BranchInfo;
use App\Models\WebSetup\SidebarNav;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class UsersController extends Controller
{
    /* public function __construct()
    {
        $this->middleware('permission:create_user', ['only' => ['create']]);
        $this->middleware('permission:view_user', ['only' => ['index']]);
        $this->middleware('permission:update_user', ['only' => ['edit']]);
        $this->middleware('permission:delete_user', ['only' => ['destroy']]);
    } */
    public function LoginFrom()
    {
        return view('auth.login');
    }

    public function index()
    {
        // $this->checkLogin();
        return view('UserConfig.user.showUser');
    }
    public function create()
    {
        // $this->checkLogin();
        return view('UserConfig.user.createUser');
    }
    public function edit($id)
    {
        // $this->checkLogin();
        $rowItem = User::where('uid', $id)->first();
        return view('UserConfig.user.editUser', ['rowItem' => $rowItem]);
    }
    public function store(Request $request){
        try {
            if ($request['id']==""){
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required',
                    
                    'phone' => 'required',
                    'roles' => 'required',
                    'status' => 'required',
                    'password' => 'required',
                ]);

                if ($validator->fails()) {
                    return json_encode(array('statusCode' => 204,'statusMsg' => 'Validation Error.', 'errors' => $validator->errors()));
                }
                $user = User::create([
                    'uid' => Str::uuid(),
                    'name'=>$request->name,
                    
                    'email'=>$request->email,
                    'phone'=>$request->phone,
                    'status'=>$request->status,
                    'password'=>Hash::make($request->password)
                ]);
                $user->syncRoles($request->roles);
                return json_encode(array(
                    "statusCode" => 200,
                    "statusMsg" => "Data Added Successfully"
                ));

            }
            else{
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required',
                    'branch_id' => 'required',
                    'phone' => 'required',
                    'roles' => 'required',
                    'status' => 'required',
                ]);

                if ($validator->fails()) {
                    return json_encode(array('statusCode' => 204,'statusMsg' => 'Validation Error.', 'errors' => $validator->errors()));
                }

                $id = $request['id'];
                $permission = User::findOrFail($id);
                $permission->update([
                    'name'=>$request->name,
                    'branch_id'=>$request->branch_id,
                    'email'=>$request->email,
                    'phone'=>$request->phone,
                    'status'=>$request->status,
                    'password'=>Hash::make($request->password)
                ]);
                $permission->syncRoles($request->roles);
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
            $singleDataShow = DB::table('users')->where('id', $id)->get();
            //$singleDataShow = User::findOrFail($id)->get();
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
            $permission = User::where('uid', $id)->first();
            $permission->delete();
            return json_encode(array(
                "statusCode" => 200
            ));
        } catch (\Exception $e) {
            return json_encode(array(
                "statusCode" => 400,
                "statusMsg" => $e->getMessage()
            ));
        }
    }
    public function GetRoles(){
        try {
            $singleDataShow = Role::all();
            $singleDataShow = Role::where('name', '!=', 'Root')->get();
            return $singleDataShow;
        } catch (\Exception $e) {

            return json_encode(array(
                "statusCode" => 400,
                "statusMsg" => $e->getMessage()
            ));;
        }
    }
    public function GetBranch(){
        try {
            $singleDataShow = BranchInfo::all();
            return $singleDataShow;
        } catch (\Exception $e) {
            return json_encode(array(
                "statusCode" => 400,
                "statusMsg" => $e->getMessage()
            ));;
        }
    }
    public function getData(Request $request)
    {
        
        $query = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select(
                'users.id',
                'users.name',
                'users.uid',
                'users.phone',
                'users.email',
                'roles.name as role_name'
            )
            ->where('model_has_roles.model_type', '=', 'App\\Models\\User'); // Ensure the model type matches

        if ($request->has('search') && isset($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($query) use ($search) {
                $query->where('users.name', 'like', '%' . $search . '%')
                    ->orWhere('users.email', 'like', '%' . $search . '%')
                    ->orWhere('users.phone', 'like', '%' . $search . '%');
            });
        }

        // Apply ordering
        if ($request->has('order')) {
            $orderColumnIndex = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            $orderColumn = $request->columns[$orderColumnIndex]['data'];

            $query->orderBy($orderColumn, $orderDirection);
        }

        // Get total records count
        $totalCount = $query->count();

        // Apply pagination
        $filteredCount = $totalCount;
        $data = $query->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get()
            ->groupBy('id'); // Group by user ID to process roles later

        // Format the data for DataTables
        $formattedData = $data->map(function ($rolesByUser) {
            $user = $rolesByUser->first();
            $roleBadges = $rolesByUser->pluck('role_name')->map(function ($role) {
                return '<span class="badge bg-success">' . $role . '</span>';
            })->implode('');

            return [
                'id' => $user->id,
                'uid' => $user->uid,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'roles_html' => $roleBadges,
            ];
        })->values(); // Convert to array

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $formattedData,
        ]);
    }
    public function getData1(Request $request)
    {
        $query = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select(
                'users.id',
                'users.name',
                'users.branch_id',
                'users.phone',
                'users.email',
                'roles.name as role_name'
            )
            ->where('model_has_roles.model_type', '=', 'App\\Models\\User');

        if ($request->has('search') && isset($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function($query) use ($search) {
                $query->where('users.name', 'like', '%' . $search . '%')
                    ->orWhere('users.email', 'like', '%' . $search . '%')
                    ->orWhere('users.phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('order')) {
            $orderColumnIndex = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            $orderColumn = $request->columns[$orderColumnIndex]['data'];

            $query->orderBy($orderColumn, $orderDirection);
        }

        $totalCount = $query->count();
        $filteredCount = $totalCount;

        $data = $query->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get()
            ->groupBy('id');

        $formattedData = $data->map(function ($rolesByUser) {
            $user = $rolesByUser->first();
            $roleBadges = $rolesByUser->pluck('role_name')->map(function ($role) {
                return '<span class="badge rounded-pill bg-label-info">' . $role . '</span>';
            })->implode('</br>');

            return [
                'id' => $user->id,
                'name' => $user->name,
                'branch_id' => $user->branch_id,
                'phone' => $user->phone,
                'email' => $user->email,
                'roles_html' => $roleBadges,
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filteredCount,
            'data' => $formattedData,
        ]);
    }
    public function authenticate(Request $request) {
        try {
            // Validation rules including latitude and longitude
            $validator = Validator::make($request->all(), [
                'password' => 'required',
                'email' => 'required|email',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return json_encode(array(
                    'statusCode' => 204,
                    'statusMsg' => 'Validation Error.',
                    'errors' => $validator->errors(),
                ));
            }

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Update user location if provided
                if ($request->has('latitude') && $request->has('longitude')) {
                    $user->latitude = $request->input('latitude');
                    $user->longitude = $request->input('longitude');
                    $user->save();
                }

                return json_encode(array(
                    'statusCode' => 200,
                    'route' => 'Dashboard',
                ));
            } else {
                return json_encode(array(
                    'statusCode' => 201,
                    'statusMsg' => 'Invalid credentials.',
                ));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return json_encode(array(
                'statusCode' => 500,
                'statusMsg' => $e->getMessage(),
            ));
        }
    }
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
}
