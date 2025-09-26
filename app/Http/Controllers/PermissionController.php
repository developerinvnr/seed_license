<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $permissions = Permission::orderBy('group_name')
            ->get()
            ->groupBy('group_name')
            ->map(function ($group) {
                return $group->pluck('name')->toArray();
            })->toArray();

        return view('license.permission', compact('permissions'));
    }
}