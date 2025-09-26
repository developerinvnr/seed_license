<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\LicenseName;
use App\Models\LicenseType;
use App\Models\CoreState;
use App\Models\CoreDistrict;
use App\Models\CoreCityVillage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LicenseNameController extends Controller
{
    public function index()
    {
        $licenseNames = LicenseName::latest()->get();
        $licenseTypes = LicenseType::all();
        $states = CoreState::where('country_id', 1)->get();
        $labels = DB::table('label_sub_field_map as map')
            ->join('license_labels as labels', 'map.label_id', '=', 'labels.id')
            ->select('labels.id', 'labels.label_name')
            ->distinct()
            ->get();
        return view('license.license_name', compact('licenseNames', 'licenseTypes', 'states', 'labels'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'license_type_id' => 'required|exists:license_types,id',
                'license_name' => 'required|unique:license_names,license_name|max:255',
                'department_name' => 'required|max:255',
                'state_id' => 'required|exists:core_state,id',
                'district_id' => 'required|exists:core_district,id',
                'city_village_id' => 'required|exists:core_city_village,id',
                'pincode' => 'required|string|max:10',
                'fields' => 'nullable|array',
            ]);

            $fields = is_array($request->fields) ? implode(',', $request->fields) : '';

            LicenseName::create([
                'license_type_id' => $request->license_type_id,
                'license_name' => $request->license_name,
                'department_name' => $request->department_name,
                'state_id' => $request->state_id,
                'district_id' => $request->district_id,
                'city_village_id' => $request->city_village_id,
                'pincode' => $request->pincode,
                'fields' => $fields
            ]);

            return redirect()->route('license-name')->with('success', 'License Name added successfully.');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            if ($errors->has('license_name')) {
                return redirect()->back()->withErrors(['license_name' => 'The license name already exists.'])->withInput();
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }
    }

    public function getFields($id)
    {
        $licenseName = LicenseName::find($id);

        if (!$licenseName) {
            return response()->json(['fields' => []]);
        }

        $fields = !empty($licenseName->fields) ? array_map('trim', explode(',', $licenseName->fields)) : [];
        return response()->json(['fields' => $fields]);
    }

    public function edit($id)
    {
        $license = LicenseName::find($id);
        if (!$license) {
            return response()->json(['error' => 'License not found.'], 404);
        }

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

    public function update(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:license_names,id',
                'license_type_id' => 'required|exists:license_types,id',
                'license_name' => 'required|string|max:255|unique:license_names,license_name,' . $request->id,
                'department_name' => 'required|string|max:255',
                'state_id' => 'required|exists:core_state,id',
                'district_id' => 'required|exists:core_district,id',
                'city_village_id' => 'required|exists:core_city_village,id',
                'pincode' => 'required|string|max:10',
                'fields' => 'nullable|array',
            ]);

            $fields = is_array($request->fields) ? implode(',', $request->fields) : '';

            $license = LicenseName::findOrFail($request->id);
            $license->update([
                'license_type_id' => $request->license_type_id,
                'license_name' => $request->license_name,
                'department_name' => $request->department_name,
                'state_id' => $request->state_id,
                'district_id' => $request->district_id,
                'city_village_id' => $request->city_village_id,
                'pincode' => $request->pincode,
                'fields' => $fields
            ]);
            return redirect()->route('license-name')->with('success', 'License updated successfully.');
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            if ($errors->has('license_name')) {
                return redirect()->back()->withErrors(['license_name' => 'The license name already exists.'])->withInput();
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }
    }

    public function getDistricts($stateId)
    {
        $districts = CoreDistrict::where('state_id', $stateId)->get();
        return response()->json(['districts' => $districts]);
    }

    public function getCityVillages($districtId)
    {
        $cityVillages = CoreCityVillage::where('district_id', $districtId)->select('id', 'city_village_name', 'pincode')->get();
        return response()->json(['cityVillages' => $cityVillages]);
    }

    public function getSubFields($labelId)
    {
        $subFields = DB::table('label_sub_field_map as map')
            ->join('license_label_sub_fields as sub_fields', 'map.sub_field_id', '=', 'sub_fields.id')
            ->where('map.label_id', $labelId)
            ->select('sub_fields.id', 'sub_fields.field_name')
            ->get();
        return response()->json(['subFields' => $subFields]);
    }
}