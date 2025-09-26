<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LicenseType;
use Illuminate\Support\Facades\DB;

class LicenseTypeController extends Controller
{
    public function index()
    {
        $licenseTypes = LicenseType::all();
        $labels = DB::table('label_sub_field_map as map')
            ->join('license_labels as labels', 'map.label_id', '=', 'labels.id')
            ->select('labels.id', 'labels.label_name')
            ->distinct()
            ->get();
        return view('license.license_type', compact('licenseTypes', 'labels'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'license_type' => 'required|string|max:255|unique:license_types,license_type',
    //         'fields' => 'nullable|array',
    //     ]);

    //     $fields = is_array($request->fields) ? implode(', ', $request->fields) : '';

    //     LicenseType::create([
    //         'license_type' => $request->license_type,
    //         'fields' => $fields
    //     ]);

    //     return redirect()->route('license-type')->with('success', 'License Type added successfully.');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'license_type' => 'required|string|max:255|unique:license_types,license_type',
            'fields' => 'nullable|array',
        ]);

        $fields = is_array($request->fields) ? implode(',', $request->fields) : '';

        LicenseType::create([
            'license_type' => $request->license_type,
            'fields' => $fields
        ]);

        return redirect()->route('license-type')->with('success', 'License Type added successfully.');
    }

    public function update(Request $request)
    {
        $license = LicenseType::find($request->id);

        $license->license_type = $request->license_type;
        $license->fields = is_array($request->fields) ? implode(',', $request->fields) : '';

        $license->save();

        return redirect()->back()->with('success', 'License updated successfully!');
    }


    public function getFields($id)
    {
        $licenseType = LicenseType::find($id);

        if (!$licenseType) {
            return response()->json(['fields' => []]);
        }

        $fields = !empty($licenseType->fields) ? array_map('trim', explode(',', $licenseType->fields)) : [];
        return response()->json(['fields' => $fields]);
    }

    // public function edit($id)
    // {
    //     $license = LicenseType::find($id);
    //     if (!$license) {
    //         return response()->json(['error' => 'License not found.'], 404);
    //     }

    //     $selectedFields = !empty($license->fields) ? $license->fields : '';

    //     return response()->json([
    //         'license' => $license,
    //         'selectedFields' => $selectedFields,
    //     ]);
    // }

    public function edit($id)
    {
        $license = LicenseType::find($id);
        if (!$license) {
            return response()->json(['error' => 'License not found.'], 404);
        }

        // Get all available labels
        $labels = DB::table('label_sub_field_map as map')
            ->join('license_labels as labels', 'map.label_id', '=', 'labels.id')
            ->select('labels.id', 'labels.label_name')
            ->distinct()
            ->get();

        return response()->json([
            'license' => $license,
            'labels' => $labels,
        ]);
    }

    // public function update(Request $request)
    // {
    //     $license = LicenseType::find($request->id);
    //     $license->license_type = $request->license_type;
    //     $license->fields = is_array($request->fields) ? implode(',', $request->fields) : '';

    //     $license->save();

    //     return redirect()->back()->with('success', 'License updated successfully!');
    // }
}
