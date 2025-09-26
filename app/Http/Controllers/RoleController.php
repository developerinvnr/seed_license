<?php
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\DB;
// use Spatie\Permission\Models\Permission;
// use Spatie\Permission\Models\Role;
// use Illuminate\Support\Facades\Validator;

// class RoleController extends Controller
// {
//     public function __construct()
//     {
//         $this->middleware('auth');
//     }

//     public function index()
//     {
//         $roles = Role::where('id', '!=', 1)->orderBy('id', 'DESC')->get();
//         $permissions = Permission::orderBy('group_name')
//             ->get()
//             ->groupBy('module')
//             ->map(function ($moduleItems) {
//                 return $moduleItems->groupBy('group_name')->map(function ($groupItems) {
//                     return $groupItems->map(function ($item) {
//                         return [
//                             'id' => $item->id,
//                             'name' => $item->name,
//                         ];
//                     })->toArray();
//                 })->toArray();
//             })->toArray();

//         return view('license.role', compact('roles', 'permissions'));
//     }

//     public function store(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'role_name' => 'required|unique:roles,name',
//             'permission' => 'required|array|min:1',
//             'permission.*' => 'exists:permissions,name',
//         ]);

//         if ($validator->fails()) {
//             Log::error('Validation Errors:', $validator->errors()->toArray());
//             return redirect()->back()->withErrors($validator)->withInput();
//         }

//         DB::beginTransaction();
//         try {
//             $role = Role::create([
//                 'name' => $request->input('role_name'),
//                 'guard_name' => 'web',
//                 'status' => 'active',
//                 'can_delete' => 'Y',
//             ]);

//             $role->syncPermissions($request->input('permission'));
//             DB::commit();
//             return redirect()->route('users.role')->with('success', 'Role created successfully');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Role Creation Failed:', ['error' => $e->getMessage()]);
//             return redirect()->back()->with('error', 'Failed to create role: ' . $e->getMessage())->withInput();
//         }
//     }

//     public function edit($id)
//     {
//         $role = Role::findOrFail($id);
//         $permissions = Permission::orderBy('group_name')
//             ->get()
//             ->groupBy('module')
//             ->map(function ($moduleItems) {
//                 return $moduleItems->groupBy('group_name')->map(function ($groupItems) {
//                     return $groupItems->map(function ($item) {
//                         return [
//                             'id' => $item->id,
//                             'name' => $item->name,
//                         ];
//                     })->toArray();
//                 })->toArray();
//             })->toArray();

//         $rolePermissions = $role->permissions ? $role->permissions->pluck('name')->toArray() : [];

//         return view('license.role_edit', compact('role', 'permissions', 'rolePermissions'));
//     }

//     public function update(Request $request, $id)
//     {
//         $validator = Validator::make($request->all(), [
//             'role_name' => 'required|unique:roles,name,' . $id,
//             'permission' => 'required|array|min:1',
//             'permission.*' => 'exists:permissions,name',
//         ]);

//         if ($validator->fails()) {
//             Log::error('Validation Errors:', $validator->errors()->toArray());
//             return redirect()->back()->withErrors($validator)->withInput();
//         }

//         DB::beginTransaction();
//         try {
//             $role = Role::findOrFail($id);
//             $role->update(['name' => $request->input('role_name')]);
//             $role->syncPermissions($request->input('permission'));
//             DB::commit();
//             return redirect()->route('users.role')->with('success', 'Role updated successfully');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Role Update Failed:', ['error' => $e->getMessage()]);
//             return redirect()->back()->with('error', 'Failed to update role: ' . $e->getMessage())->withInput();
//         }
//     }

//     public function destroy($id)
//     {
//         try {
//             $role = Role::findOrFail($id);
//             if ($role->can_delete !== 'Y') {
//                 return response()->json(['status' => 403, 'message' => 'This role cannot be deleted']);
//             }
//             $role->delete();
//             return response()->json(['status' => 200, 'message' => 'Role deleted successfully']);
//         } catch (\Exception $e) {
//             Log::error('Role Deletion Failed:', ['error' => $e->getMessage()]);
//             return response()->json(['status' => 500, 'message' => 'Failed to delete role: ' . $e->getMessage()]);
//         }
//     }
// }


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $roles = Role::where('id', '!=', 1)->orderBy('id', 'DESC')->get();
        $permissions = Permission::orderBy('group_name')
            ->get()
            ->groupBy('module')
            ->map(function ($moduleItems) {
                return $moduleItems->groupBy('group_name')->map(function ($groupItems) {
                    return $groupItems->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                        ];
                    })->toArray();
                })->toArray();
            })->toArray();

        return view('license.role', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|unique:roles,name',
            'permission' => 'required|array|min:1',
            'permission.*' => 'exists:permissions,name',
        ]);

        if ($validator->fails()) {
            Log::error('Validation Errors:', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->input('role_name'),
                'guard_name' => 'web',
                'status' => 'active',
                'can_delete' => $request->input('role_name') === 'Super Admin' ? 'N' : 'Y',
            ]);

            // If role is Super Admin, assign all permissions
            if ($request->input('role_name') === 'Super Admin') {
                $allPermissions = Permission::pluck('name')->toArray();
                $role->syncPermissions($allPermissions);
            } else {
                $role->syncPermissions($request->input('permission'));
            }

            DB::commit();
            return redirect()->route('users.role')->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Creation Failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create role: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::orderBy('group_name')
            ->get()
            ->groupBy('module')
            ->map(function ($moduleItems) {
                return $moduleItems->groupBy('group_name')->map(function ($groupItems) {
                    return $groupItems->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                        ];
                    })->toArray();
                })->toArray();
            })->toArray();

        $rolePermissions = $role->permissions ? $role->permissions->pluck('name')->toArray() : [];
        $isSuperAdmin = $role->name === 'Super Admin';

        return view('license.role_edit', compact('role', 'permissions', 'rolePermissions', 'isSuperAdmin'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|unique:roles,name,' . $id,
            'permission' => 'required|array|min:1',
            'permission.*' => 'exists:permissions,name',
        ]);

        if ($validator->fails()) {
            Log::error('Validation Errors:', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->input('role_name'),
                'can_delete' => $request->input('role_name') === 'Super Admin' ? 'N' : 'Y',
            ]);

            // If role is Super Admin, assign all permissions
            if ($request->input('role_name') === 'Super Admin') {
                $allPermissions = Permission::pluck('name')->toArray();
                $role->syncPermissions($allPermissions);
            } else {
                $role->syncPermissions($request->input('permission'));
            }

            DB::commit();
            return redirect()->route('users.role')->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Role Update Failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update role: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            if ($role->can_delete !== 'Y') {
                return response()->json(['status' => 403, 'message' => 'This role cannot be deleted']);
            }
            $role->delete();
            return response()->json(['status' => 200, 'message' => 'Role deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Role Deletion Failed:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 500, 'message' => 'Failed to delete role: ' . $e->getMessage()]);
        }
    }
}