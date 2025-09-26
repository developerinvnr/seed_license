<?php

namespace App\Http\Controllers;

use App\Models\LicenseType;
use App\Models\ResponsibleMaster;
use App\Models\LicenseName;
use App\Models\License;
use App\Models\CoreState;
use App\Models\CoreCompany;
use App\Models\HrmGroupcom;
use App\Models\CoreDistrict;
use App\Models\CoreCityVillage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Mail\LicenseExpirationReminder;
use Illuminate\Support\Facades\Mail;
use Spatie\Activitylog\Facades\Activity;
use Yajra\DataTables\DataTables;


class LicenseListController extends Controller
{
    // public function index()
    // {
    //     $licenses = License::with(['licenseType', 'licenseName', 'company', 'groupcom', 'state', 'district', 'cityVillage'])
    //         ->where('lis_status', 'Active')
    //         ->latest()
    //         ->get();
    //     $licenseTypes = LicenseType::all();
    //     $responsibles = ResponsibleMaster::all();
    //     $states = CoreState::where('country_id', 1)->get();
    //     $companies = CoreCompany::all();
    //     return view('license.license_list', compact('licenses', 'licenseTypes', 'responsibles', 'states', 'companies'));
    // }

    public function index(Request $request)
    {
        $query = License::with(['licenseType', 'licenseName', 'company', 'groupcom', 'state', 'district', 'cityVillage'])
            ->where('lis_status', 'Active')
            ->latest();

        if ($request->has('company_id') && $request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        $licenses = $query->get();
        $licenseTypes = LicenseType::all();
        $responsibles = ResponsibleMaster::all();
        $states = CoreState::where('country_id', 1)->get();
        $companies = CoreCompany::all();
        return view('license.license_list', compact('licenses', 'licenseTypes', 'responsibles', 'states', 'companies'));
    }

    public function getGroupCompanies(Request $request)
    {
        $companyId = $request->query('company_id');
        if (!$companyId) {
            return response()->json(['groupcom' => []], 400);
        }

        $groupcomCompanies = DB::table('hrm_groupcom')
            ->where('core_company_id', $companyId)
            ->select('id', 'name')
            ->get();

        if ($groupcomCompanies->isEmpty()) {
            $company = CoreCompany::find($companyId);
            return response()->json([
                'groupcom' => [[
                    'id' => null,
                    'name' => $company ? $company->company_name : 'N/A'
                ]]
            ]);
        }

        return response()->json(['groupcom' => $groupcomCompanies]);
    }

    public function getLicenseNames($licenseTypeId)
    {
        $licenseNames = LicenseName::where('license_type_id', $licenseTypeId)->get();
        $licenseType = LicenseType::find($licenseTypeId);
        $fields = [];
        if ($licenseType && $licenseType->fields) {
            $fieldArray = array_map('trim', explode(',', $licenseType->fields));
            foreach ($fieldArray as $field) {
                $fields[] = ['field_name' => $field];
            }
        }
        return response()->json([
            'license_names' => $licenseNames,
            'fields' => $fields,
        ]);
    }

    public function getLicenseAddress($id)
    {
        try {
            $licenseName = LicenseName::where('id', $id)
                ->select('state_id', 'district_id', 'city_village_id', 'pincode')
                ->first();

            if (!$licenseName) {
                return response()->json(['error' => 'License name not found'], 404);
            }

            return response()->json([
                'state_id' => $licenseName->state_id,
                'district_id' => $licenseName->district_id,
                'city_village_id' => $licenseName->city_village_id,
                'pincode' => $licenseName->pincode
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching license address: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch address details'], 500);
        }
    }

    public function getMappedFields($id)
    {
        $license = LicenseName::find($id);
        if (!$license) {
            return response()->json(['fields' => []]);
        }

        $labelIds = array_filter(explode(',', $license->fields));
        $subFieldMappings = DB::table('label_sub_field_map')
            ->whereIn('label_id', $labelIds)
            ->get()
            ->groupBy('label_id');

        $fieldsGrouped = [];
        foreach ($subFieldMappings as $labelId => $mappings) {
            $label = DB::table('license_labels')->where('id', $labelId)->first();
            if (!$label) continue;
            $subFieldIds = $mappings->pluck('sub_field_id')->toArray();
            $fieldDetails = DB::table('license_label_sub_fields')
                ->whereIn('id', $subFieldIds)
                ->get();
            $fields = [];
            foreach ($fieldDetails as $field) {
                $entry = [
                    'field_name' => $field->field_name,
                    'sub_field_id' => $field->id,
                    'input_type' => $field->input_type,
                    'options' => [],
                ];
                if ($field->input_type === 'select' && $field->table_name && $field->column_name) {
                    $options = DB::table($field->table_name)->pluck($field->column_name)->toArray();
                    $entry['options'] = array_map(function ($opt) {
                        return ['label' => $opt, 'value' => $opt];
                    }, $options);
                }
                $fields[] = $entry;
            }
            $fieldsGrouped[] = [
                'label_id' => $label->id,
                'label_name' => $label->label_name,
                'fields' => $fields
            ];
        }
        return response()->json(['fields' => $fieldsGrouped]);
    }

    public function getLicenseDetails($id)
    {
        $license = License::with(['company', 'groupcom', 'licenseType', 'licenseName', 'state', 'district', 'cityVillage','documents'])->find($id);
        if (!$license) {
            return response()->json(['error' => 'License not found'], 404);
        }

        $licenseData = $license->toArray();
        if (!$license->groupcom && $license->company) {
            $licenseData['groupcom'] = [
                'id' => null,
                'name' => $license->company->company_name
            ];
        }

        return response()->json(['license' => $licenseData]);
    }

    public function getResponsibleDetails(Request $request)
    {
        $licenseTypeId = $request->input('license_type_id');
        $licenseNameId = $request->input('license_name_id');

        // Check if inputs are provided
        if (!$licenseTypeId || !$licenseNameId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please select both license type and license name.',
                'data' => []
            ], 400);
        }

        // Find the responsible person
        $responsible = ResponsibleMaster::join('responsible_masters_license_details as rld', 'responsible_masters.id', '=', 'rld.responsible_master_id')
            ->join('core_employee', 'responsible_masters.core_employee_id', '=', 'core_employee.id')
            ->where('rld.license_type_id', $licenseTypeId)
            ->where('rld.license_name_id', $licenseNameId)
            ->where('responsible_masters.Authorization_status', 'Active')
            ->select(
                'core_employee.id as emp_id',
                'core_employee.emp_name',
                'core_employee.emp_email',
                'core_employee.emp_contact',
                'core_employee.emp_department',
                'core_employee.emp_designation'
            )
            ->first();

        // Return data or empty array
        return response()->json([
            'status' => 'success',
            'data' => $responsible ? $responsible : []
        ], 200);
    }

    public function checkLicenseNameResponsible($licenseNameId)
    {
        $exists = DB::table('responsible_masters_license_details')
            ->where('license_name_id', $licenseNameId)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function store(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:core_company,id',
            'groupcom_id' => 'nullable|exists:hrm_groupcom,id',
            'license_type_id' => 'required|exists:license_types,id',
            'license_name_id' => 'required|exists:license_names,id',
            'state_id' => 'required|exists:core_state,id',
            'district_id' => 'required|exists:core_district,id',
            'city_village_id' => 'required|exists:core_city_village,id',
            'pincode' => 'required|string|max:10',
            'responsible_person' => 'required|string',
            'res_email' => 'required|email',
            'res_contact' => 'required|string',
            'res_department' => 'required|string',
            'res_designation' => 'required|string',
            'application_number' => 'required|string|max:255',
            'letter_date' => 'required|date',
            'date_of_issue' => 'required|date',
            'registration_number' => 'required|string|max:255',
            'certificate_number' => 'required|string|max:255',
            'valid_upto' => 'required|date',
            'reminder_option' => 'required|in:Y,N',
            'reminder_emails' => 'required_if:reminder_option,Y|array',
            'reminder_emails.*' => 'required|email',
            'lis_status' => 'required|in:Active,Deactive',
            'license_creation' => 'required|in:new,modification',
            'license_creation_remark' => 'required_if:license_creation,modification|string|nullable',
            'application_document.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'application_status' => 'required|in:Submitted,Under review,Approved,Withdrawn,Rejected',
        ]);
        

        if ($validator->fails()) { 
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $existingLicense = License::where('license_type_id', $request->license_type_id)
            ->where('license_name_id', $request->license_name_id)
            ->where('license_performance', 'new')
            ->first();

        if ($existingLicense) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A license with this License Type and License Name already exists and is active. Please renew the existing license instead.');
        }

        $licenseData = [
            'company_id' => $request->company_id,
            'groupcom_id' => $request->groupcom_id ?: null,
            'license_type_id' => $request->license_type_id,
            'license_name_id' => $request->license_name_id,
            'state_id' => $request->state_id,
            'district_id' => $request->district_id,
            'city_village_id' => $request->city_village_id,
            'pincode' => $request->pincode,
            'responsible_person' => $request->responsible_person,
            'res_email' => $request->res_email,
            'res_contact' => $request->res_contact,
            'res_department' => $request->res_department,
            'res_designation' => $request->res_designation,
            'application_number' => $request->application_number,
            'letter_date' => $request->letter_date,
            'date_of_issue' => $request->date_of_issue,
            'registration_number' => $request->registration_number,
            'certificate_number' => $request->certificate_number,
            'valid_upto' => $request->valid_upto,
            'reminder_option' => $request->reminder_option,
            'lis_status' => $request->lis_status,
            'license_performance' => $request->license_creation,
            'license_creation_remark' => $request->license_creation_remark,
            'application_status' => $request->application_status,
        ];

        if ($request->reminder_option === 'Y' && $request->filled('reminder_emails')) {
            $licenseData['reminder_emails'] = implode(',', array_map('trim', $request->reminder_emails));
        }

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'errors' => $validator->errors()
        //     ], 422);
        // }


        $license = License::create($licenseData);

        // Inside the store method, after $license = License::create($licenseData);
        if ($request->hasFile('application_document')) {
            $documents = $request->file('application_document');
            $documentNames = $request->input('document_name', []); 
            foreach ($documents as $index => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('application_documents', $fileName, 'public');
                $license->documents()->create([
                    'document_type' => 'application',
                    'file_path' => $path,
                    'file_name' => $fileName,
                    'document_name' => $documentNames[$index] ?? $fileName,
                ]);
            }
        }

        $mappedFields = $request->input('mapped_fields', []);
        foreach ($mappedFields as $tableName => $rows) {
            if (!preg_match('/^label_\d+$/', $tableName) || !Schema::hasTable($tableName)) {
                continue;
            }
            foreach ($rows as $rowIndex => $fields) {
                $insertData = [];
                foreach ($fields as $columnName => $value) {
                    if (!preg_match('/^field_name_\d+$/', $columnName) || !Schema::hasColumn($tableName, $columnName)) {
                        continue;
                    }
                    if ($request->hasFile("mapped_fields.$tableName.$rowIndex.$columnName")) {
                        $file = $request->file("mapped_fields.$tableName.$rowIndex.$columnName");
                        $path = $file->store('uploads', 'public');
                        $insertData[$columnName] = $path;
                    } else {
                        $insertData[$columnName] = $value;
                    }
                }
                if (Schema::hasColumn($tableName, 'licenses_id')) {
                    $insertData['licenses_id'] = $license->id;
                }
                if (Schema::hasColumn($tableName, 'created_at')) {
                    $insertData['created_at'] = now();
                }
                if (Schema::hasColumn($tableName, 'updated_at')) {
                    $insertData['updated_at'] = now();
                }
                if (!empty($insertData)) {
                    DB::table($tableName)->insert($insertData);
                }
            }
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($license)
            ->event('created_dynamic')
            ->withProperties(['mapped_fields' => $mappedFields])
            ->log('License created with dynamic fields');

        return redirect()->route('license-list')->with('success', 'License and dynamic data saved successfully.');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:core_company,id',
            'groupcom_id' => 'nullable|exists:hrm_groupcom,id',
            'license_id' => 'required|exists:licenses,id',
            'license_type_id' => 'required|exists:license_types,id',
            'license_name_id' => 'required|exists:license_names,id',
            'state_id' => 'required|exists:core_state,id',
            'district_id' => 'required|exists:core_district,id',
            'city_village_id' => 'required|exists:core_city_village,id',
            'pincode' => 'required|string|max:10',
            'responsible_person' => 'required|string',
            'res_email' => 'required|email',
            'res_contact' => 'required|string',
            'res_department' => 'required|string',
            'res_designation' => 'required|string',
            'application_number' => 'required|string|max:255',
            'letter_date' => 'required|date',
            'date_of_issue' => 'required|date',
            'registration_number' => 'required|string|max:255',
            'certificate_number' => 'required|string|max:255',
            'valid_upto' => 'required|date',
            'reminder_option' => 'required|in:Y,N',
            'reminder_emails' => 'required_if:reminder_option,Y|array',
            'reminder_emails.*' => 'required|email',
            'lis_status' => 'required|in:Active,Deactive',
            'application_document.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'application_status' => 'required|in:Submitted,Under review,Approved,Withdrawn,Rejected',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        $license = License::findOrFail($request->license_id);

        $licenseData = [
            'company_id' => $request->company_id,
            'groupcom_id' => $request->groupcom_id ?: null,
            'license_type_id' => $request->license_type_id,
            'license_name_id' => $request->license_name_id,
            'state_id' => $request->state_id,
            'district_id' => $request->district_id,
            'city_village_id' => $request->city_village_id,
            'pincode' => $request->pincode,
            'responsible_person' => $request->responsible_person,
            'res_email' => $request->res_email,
            'res_contact' => $request->res_contact,
            'res_department' => $request->res_department,
            'res_designation' => $request->res_designation,
            'application_number' => $request->application_number,
            'letter_date' => $request->letter_date,
            'date_of_issue' => $request->date_of_issue,
            'registration_number' => $request->registration_number,
            'certificate_number' => $request->certificate_number,
            'valid_upto' => $request->valid_upto,
            'reminder_option' => $request->reminder_option,
            'lis_status' => $request->lis_status,
            'application_status' => $request->application_status,
        ];

        if ($request->reminder_option === 'Y' && $request->filled('reminder_emails')) {
            $licenseData['reminder_emails'] = implode(',', array_map('trim', $request->reminder_emails));
        } else {
            $licenseData['reminder_emails'] = null;
        }

        $license->update($licenseData);

        // Inside the update method, after $license->update($licenseData);
        if ($request->hasFile('application_document')) {
            $documents = $request->file('application_document');
            $documentNames = $request->input('document_name', []);
            foreach ($documents as $index => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('application_documents', $fileName, 'public');
                $license->documents()->create([
                    'document_type' => 'application',
                    'file_path' => $path,
                    'file_name' => $fileName,
                    'document_name' => $documentNames[$index] ?? $fileName,
                ]);
            }
        }

        $mappedFields = $request->input('mapped_fields', []);
        $licenseName = LicenseName::find($request->license_name_id);
        if ($licenseName && $licenseName->fields) {
            $labelIds = array_filter(explode(',', $licenseName->fields));
            foreach ($labelIds as $labelId) {
                $tableName = "label_{$labelId}";
                if (Schema::hasTable($tableName)) {
                    DB::table($tableName)
                        ->where('licenses_id', $license->id)
                        ->delete();
                }
            }
        }

        foreach ($mappedFields as $tableName => $rows) {
            if (!preg_match('/^label_\d+$/', $tableName) || !Schema::hasTable($tableName)) {
                continue;
            }
            foreach ($rows as $rowIndex => $fields) {
                $insertData = [];
                foreach ($fields as $columnName => $value) {
                    if (!preg_match('/^field_name_\d+$/', $columnName) || !Schema::hasColumn($tableName, $columnName)) {
                        continue;
                    }
                    if ($request->hasFile("mapped_fields.$tableName.$rowIndex.$columnName")) {
                        $file = $request->file("mapped_fields.$tableName.$rowIndex.$columnName");
                        $path = $file->store('uploads', 'public');
                        $insertData[$columnName] = $path;
                    } else {
                        $insertData[$columnName] = $value;
                    }
                }
                if (Schema::hasColumn($tableName, 'licenses_id')) {
                    $insertData['licenses_id'] = $license->id;
                }
                if (Schema::hasColumn($tableName, 'created_at')) {
                    $insertData['created_at'] = now();
                }
                if (Schema::hasColumn($tableName, 'updated_at')) {
                    $insertData['updated_at'] = now();
                }
                if (!empty($insertData)) {
                    DB::table($tableName)->insert($insertData);
                }
            }
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($license)
            ->event('updated_dynamic')
            ->withProperties(['mapped_fields' => $mappedFields])
            ->log('License updated with dynamic fields');

        return redirect()->route('license-list')->with('success', 'License updated successfully.');
    }

    public function renew(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:core_company,id',
            'groupcom_id' => 'nullable|exists:hrm_groupcom,id',
            'license_id' => 'required|exists:licenses,id',
            'license_type_id' => 'required|exists:license_types,id',
            'license_name_id' => 'required|exists:license_names,id',
            'state_id' => 'required|exists:core_state,id',
            'district_id' => 'required|exists:core_district,id',
            'city_village_id' => 'required|exists:core_city_village,id',
            'pincode' => 'required|string|max:10',
            'responsible_person' => 'required|string',
            'res_email' => 'required|email',
            'res_contact' => 'required|string',
            'res_department' => 'required|string',
            'res_designation' => 'required|string',
            'application_number' => 'required|string|max:255',
            'letter_date' => 'required|date',
            'date_of_issue' => 'required|date',
            'registration_number' => 'required|string|max:255',
            'certificate_number' => 'required|string|max:255',
            'valid_upto' => 'required|date',
            'reminder_option' => 'required|in:Y,N',
            'reminder_emails' => 'required_if:reminder_option,Y|array',
            'reminder_emails.*' => 'required|email',
            'lis_status' => 'required|in:Active,Deactive',
            'license_performance' => 'required|in:new,renewed',
            'application_document.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'application_status' => 'required|in:Submitted,Under review,Approved,Withdrawn,Rejected',
        ]);  

        if ($validator->fails()) {  
            return redirect()->back()->withErrors($validator)->withInput();
        }

        //  if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        $oldLicense = License::findOrFail($request->license_id);
        $oldLicense->update(['lis_status' => 'Deactive']);

        $licenseData = [
            'company_id' => $request->company_id,
            'groupcom_id' => $request->groupcom_id ?: null,
            'license_type_id' => $request->license_type_id,
            'license_name_id' => $request->license_name_id,
            'state_id' => $request->state_id,
            'district_id' => $request->district_id,
            'city_village_id' => $request->city_village_id,
            'pincode' => $request->pincode,
            'responsible_person' => $request->responsible_person,
            'res_email' => $request->res_email,
            'res_contact' => $request->res_contact,
            'res_department' => $request->res_department,
            'res_designation' => $request->res_designation,
            'application_number' => $request->application_number,
            'letter_date' => $request->letter_date,
            'date_of_issue' => $request->date_of_issue,
            'registration_number' => $request->registration_number,
            'certificate_number' => $request->certificate_number,
            'valid_upto' => $request->valid_upto,
            'reminder_option' => $request->reminder_option,
            'lis_status' => $request->lis_status,
            'license_performance' => $request->license_performance,
            'application_status' => $request->application_status,
        ];  

        if ($request->reminder_option === 'Y' && $request->filled('reminder_emails')) {
            $licenseData['reminder_emails'] = implode(',', array_map('trim', $request->reminder_emails));
        } else {
            $licenseData['reminder_emails'] = null;
        }

        $newLicense = License::create($licenseData);

        // Inside the renew method, after $newLicense = License::create($licenseData);
        if ($request->hasFile('application_document')) {
            $documents = $request->file('application_document');
            $documentNames = $request->input('document_name', []);
            foreach ($documents as $index => $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('application_documents', $fileName, 'public');
                $newLicense->documents()->create([
                    'document_type' => 'application',
                    'file_path' => $path,
                    'file_name' => $fileName,
                    'document_name' => $documentNames[$index] ?? $fileName,
                ]);
            }
        }

        $mappedFields = $request->input('mapped_fields', []);
        foreach ($mappedFields as $tableName => $rows) {
            if (!preg_match('/^label_\d+$/', $tableName) || !Schema::hasTable($tableName)) {
                continue;
            }
            foreach ($rows as $rowIndex => $fields) {
                $insertData = [];
                foreach ($fields as $columnName => $value) {
                    if (!preg_match('/^field_name_\d+$/', $columnName) || !Schema::hasColumn($tableName, $columnName)) {
                        continue;
                    }
                    if ($request->hasFile("mapped_fields.$tableName.$rowIndex.$columnName")) {
                        $file = $request->file("mapped_fields.$tableName.$rowIndex.$columnName");
                        $path = $file->store('uploads', 'public');
                        $insertData[$columnName] = $path;
                    } else {
                        $insertData[$columnName] = $value;
                    }
                }
                if (Schema::hasColumn($tableName, 'licenses_id')) {
                    $insertData['licenses_id'] = $newLicense->id;
                }
                if (Schema::hasColumn($tableName, 'created_at')) {
                    $insertData['created_at'] = now();
                }
                if (Schema::hasColumn($tableName, 'updated_at')) {
                    $insertData['updated_at'] = now();
                }
                if (!empty($insertData)) {
                    DB::table($tableName)->insert($insertData);
                }
            }
        }

        activity()
            ->causedBy(auth()->user())
            ->performedOn($newLicense)
            ->event('renewed_dynamic')
            ->withProperties(['mapped_fields' => $mappedFields])
            ->log('License renewed with dynamic fields');

        return redirect()->route('license-list')->with('success', 'License renewed successfully.');
    }

    public function getLicenseHistory($licenseTypeId, $licenseNameId)
    {
        $licenses = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
            ->where('license_type_id', $licenseTypeId)
            ->where('license_name_id', $licenseNameId)
            ->where('lis_status', 'deactive')
            ->whereHas('licenseType')
            ->whereHas('licenseName')
            ->get()
            ->map(function ($license) {
                return [
                    'id' => $license->id,
                    'license_type_id' => $license->license_type_id,
                    'license_name_id' => $license->license_name_id,
                    'valid_upto' => $license->valid_upto,
                    'lis_status' => $license->lis_status,
                    'licenseType' => $license->licenseType ? [
                        'id' => $license->licenseType->id,
                        'license_type' => $license->licenseType->license_type
                    ] : ['id' => null, 'license_type' => 'N/A'],
                    'licenseName' => $license->licenseName ? [
                        'id' => $license->licenseName->id,
                        'license_name' => $license->licenseName->license_name
                    ] : ['id' => null, 'license_name' => 'N/A'],
                    'company' => $license->company ? [
                        'id' => $license->company->id,
                        'company_name' => $license->company->company_name
                    ] : ['id' => null, 'company_name' => 'N/A'],
                    'groupcom' => $license->groupcom ? [
                        'id' => $license->groupcom->id,
                        'name' => $license->groupcom->name
                    ] : ['id' => null, 'name' => $license->company ? $license->company->company_name : 'N/A'],
                ];
            });

        return response()->json(['licenses' => $licenses]);
    }

    public function getLabelData($licenseId, $tableName)
    {
        if (!preg_match('/^label_\d+$/', $tableName) || !Schema::hasTable($tableName)) {
            return response()->json(['rows' => []]);
        }

        $rows = DB::table($tableName)
            ->where('licenses_id', $licenseId)
            ->get()
            ->toArray();

        $rows = array_map(function ($row) {
            return (array) $row;
        }, $rows);

        return response()->json(['rows' => $rows]);
    }

    public function sendReminderEmails()
    {
        $reminderDays = [90, 45, 30, 20, 10, 5, 1];

        $datesToCheck = collect($reminderDays)->map(function ($days) {
            return Carbon::now()->addDays($days)->toDateString();
        })->toArray();

        $licenses = License::whereIn(DB::raw('DATE(valid_upto)'), $datesToCheck)
            ->where('reminder_option', 'Y')
            ->whereNotNull('reminder_emails')
            ->where(function ($query) {
                $today = Carbon::now()->toDateString();
                $query->whereNull('last_reminder_sent_at')
                    ->orWhere('last_reminder_sent_at', '<', $today);
            })
            ->get();

        foreach ($licenses as $license) {
            $emails = explode(',', $license->reminder_emails);
            foreach ($emails as $email) {
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->send(new LicenseExpirationReminder($license));
                }
            }
            $license->last_reminder_sent_at = Carbon::now()->toDateString();
            $license->save();
        }

        return response()->json(['status' => 'success', 'message' => 'Reminder emails sent successfully.']);
    }

    public function popupDetails($id)
    {
        $license = License::with(['licenseType', 'licenseName', 'state', 'district', 'cityVillage'])->findOrFail($id);
        return view('license.popup_details', compact('license'));
    }

    public function activityLog()
    {
        $activities = \Spatie\Activitylog\Models\Activity::where('log_name', 'licenses')
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('license.license_activity_log', compact('activities'));
    }

    public function getDistricts($stateId)
    {
        try {
            $districts = CoreDistrict::where('state_id', $stateId)
                ->select('id', 'district_name')
                ->get();
            return response()->json(['districts' => $districts], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch districts'], 500);
        }
    }

    public function getCityVillages($districtId)
    {
        try {
            $cityVillages = CoreCityVillage::where('district_id', $districtId)
                ->select('id', 'city_village_name', 'pincode')
                ->get();
            return response()->json(['cityVillages' => $cityVillages], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch city/villages'], 500);
        }
    }
}
