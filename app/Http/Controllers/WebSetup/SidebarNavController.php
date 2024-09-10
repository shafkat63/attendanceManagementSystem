<?php

namespace App\Http\Controllers\WebSetup;

use App\Http\Controllers\Controller;
use App\Models\WebSetup\SidebarNav;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SidebarNavController extends Controller
{
    /* public function __construct()
    {
        $this->middleware('permission:create_sidemenu', ['only' => ['create']]);
        $this->middleware('permission:view_sidemenu', ['only' => ['index']]);
        $this->middleware('permission:update_sidemenu', ['only' => ['edit']]);
        $this->middleware('permission:delete_sidemenu', ['only' => ['destroy']]);
    } */
    public function index()
    {
        $this->checkLogin();
        return view('WebSetup.SidebarNav.showSidebarNav');
    }
    public function create()
    {
        $this->checkLogin();
        $prentMenu = SidebarNav::whereNull('parent_id')->get();
        return view('WebSetup.SidebarNav.createSidebarNav', ['prentMenu' => $prentMenu]);
    }
    public function edit($id)
    {
        $this->checkLogin();
        $navItem = SidebarNav::where('uid', $id)
            ->with('children')
            ->first();
        $prentMenu = SidebarNav::whereNull('parent_id')->get();

        return view('WebSetup.SidebarNav.editSidebarNav', ['navItem' => $navItem, 'prentMenu' => $prentMenu]);
    }
    public function store(Request $request)
    {
        try {
            if ($request['id'] == "") {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'icon' => 'required',
                    'url' => 'required',
                    'order' => 'required',
                    'status' => 'required',
                ]);
                if ($validator->fails()) {
                    return json_encode(array('statusCode' => 204, 'statusMsg' => 'Validation Error.', 'errors' => $validator->errors()));
                }
                $parentId = $request->input('parent_id');
                $parentId = $parentId === '' ? null : (int)$parentId;
                $data = SidebarNav::create([
                    'uid' => Str::uuid(),
                    'parent_id' => $parentId,
                    'name' => $request->name,
                    'icon' => $request->icon ?: '',
                    'url' => $request->url ?: '',
                    'order' => $request->order ?: 0,
                    'is_collapsed' => $request->is_collapsed ?: 0,
                    'is_heading' => $request->is_heading ?: 0,
                    'status' => $request->status,
                    'create_by' => auth()->user()->id,
                    'create_date' => $this->getCurrentDateTime()
                ]);

                return json_encode(array(
                    "statusCode" => 200,
                    "statusMsg" => "Data Added Successfully"
                ));
            } else {
                $id = $request['id'];
                $navItem = SidebarNav::find($id);

                if (!$navItem) {
                    return response()->json([
                        'statusCode' => 404,
                        'statusMsg' => 'Item not found.',
                    ]);
                }

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'icon' => 'required',
                    'url' => 'required',
                    'order' => 'required',
                    'status' => 'required',
                ]);

                if ($validator->fails()) {
                    return json_encode(array('statusCode' => 204, 'statusMsg' => 'Validation Error.', 'errors' => $validator->errors()));

                }
                $parentId = $request->input('parent_id');
                $parentId = $parentId === '' ? null : (int)$parentId;
                $navItem->update([
                    'parent_id' => $parentId,
                    'name' => $request->name,
                    'icon' => $request->icon ?: '',
                    'url' => $request->url ?: '',
                    'order' => $request->order ?: 0,
                    'is_collapsed' => $request->is_collapsed ?: 0,
                    'is_heading' => $request->is_heading ?: 0,
                    'status' => $request->status,
                    'update_by' => auth()->user()->id,
                    'update_date' => $this->getCurrentDateTime()
                ]);

                return json_encode(array(
                    "statusCode" => 200,
                    "statusMsg" => "Data Added Successfully"
                ));
            }

        } catch (\Exception $e) {
            return json_encode(array(
                "statusCode" => 400,
                "statusMsg" => $e->getMessage()
            ));
        }
    }
    public function destroy($id)
    {
        try {
            $permission = SidebarNav::where('uid', $id)->first();
            $permission->update([
                'status' => "Deleted",
                'update_by' => auth()->user()->id,
                'update_date' => $this->getCurrentDateTime()
            ]);

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
    public function getData(Request $request)
    {
        // Fetch all items with their hierarchy
        $query = DB::table('sms_web_sidebar_menu as p')
            ->select('p.id','p.uid', 'p.parent_id', 'p.name', 'p.order', 'p.status')
            ->where('p.status', '!=', 'Deleted');

        // Search functionality
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
    protected function formatHierarchy($data)
    {
        $result = [];
        $items = $data->keyBy('id');
        $children = [];

        // Prepare children array
        foreach ($items as $item) {
            if ($item->parent_id) {
                $children[$item->parent_id][] = $item;
            }
        }

        // Format each item and include children
        foreach ($items as $item) {
            if (!$item->parent_id) {
                $result[] = $this->formatItem($item, $children);
            }
        }

        return $result;
    }
    protected function formatItem($item, $children)
    {
        $formatted = [
            'id' => $item->id,
            'parent_id' => $item->parent_id,
            'name' => $item->name,
            'order' => $item->order,
            'status' => $item->status,
        ];

        if (isset($children[$item->id])) {
            foreach ($children[$item->id] as $child) {
                $formatted['children'][] = $this->formatItem($child, $children);
            }
        } else {
            $formatted['children'] = [];
        }

        return $formatted;
    }
}
