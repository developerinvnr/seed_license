<?php
// namespace App\Http\Controllers;

// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Spatie\Permission\Models\Role;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Validator;

// class UserController extends Controller
// {
//     public function __construct()
//     {
//         $this->middleware('auth');
//     }

//     public function index()
//     {
//         $users = User::all();
//         $roles = Role::all();
//         return view('license.users', compact('users', 'roles'));
//     }

//     public function store(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'name' => 'required|string|max:255',
//             'email' => 'required|email|unique:users,email',
//             'password' => 'required|min:6',
//             'mobile' => 'nullable|string|max:15',
//             'role' => 'required|exists:roles,name',
//         ]);

//         if ($validator->fails()) {
//             return redirect()->back()->withErrors($validator)->withInput();
//         }

//         try {
//             $user = User::create([
//                 'name' => $request->input('name'),
//                 'email' => $request->input('email'),
//                 'password' => Hash::make($request->input('password')),
//                 'mobile' => $request->input('mobile'),
//             ]);

//             $user->assignRole($request->input('role'));

//             return redirect()->route('users.index')->with('success', 'User created successfully');
//         } catch (\Exception $e) {
//             Log::error('User Creation Failed:', ['error' => $e->getMessage()]);
//             return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage())->withInput();
//         }
//     }

//     public function update(Request $request, $id)
//     {
//         $validator = Validator::make($request->all(), [
//             'name' => 'required|string|max:255',
//             'email' => 'required|email|unique:users,email,' . $id,
//             'mobile' => 'nullable|string|max:15',
//             'role' => 'required|exists:roles,name',
//         ]);

//         if ($validator->fails()) {
//             return redirect()->back()->withErrors($validator)->withInput();
//         }

//         try {
//             $user = User::findOrFail($id);
//             $user->update([
//                 'name' => $request->input('name'),
//                 'email' => $request->input('email'),
//                 'mobile' => $request->input('mobile'),
//             ]);

//             $user->syncRoles($request->input('role'));

//             return redirect()->route('users.index')->with('success', 'User updated successfully');
//         } catch (\Exception $e) {
//             Log::error('User Update Failed:', ['error' => $e->getMessage()]);
//             return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage())->withInput();
//         }
//     }
// }


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        return view('license.users', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'mobile' => 'nullable|string|max:15',
            'role' => 'required|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'mobile' => $request->input('mobile'),
            ]);

            $role = $request->input('role');
            $user->assignRole($role);
            Log::info('User assigned role:', ['user_id' => $user->id, 'role' => $role]);

            return redirect()->route('users.index')->with('success', 'User created successfully');
        } catch (\Exception $e) {
            Log::error('User Creation Failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'mobile' => 'nullable|string|max:15',
            'role' => 'required|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::findOrFail($id);
            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
            ]);

            $role = $request->input('role');
            $user->syncRoles($role);
            Log::info('User role updated:', ['user_id' => $id, 'role' => $role]);

            return redirect()->route('users.index')->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            Log::error('User Update Failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage())->withInput();
        }
    }
}