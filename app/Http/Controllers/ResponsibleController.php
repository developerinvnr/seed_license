<?php

namespace App\Http\Controllers;
use App\Models\ResponsibleMaster;
use App\Models\ResponsibleMastersLicenseDetails;
use App\Models\LicenseType;
use App\Models\LicenseName;
use App\Models\PurposeDetail;
use App\Models\AuthDraftMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ResponsibleController extends Controller
{
    public function index()
    {
        $responsibles = ResponsibleMaster::with(['employee', 'company', 'licenseDetails', 'activities'])->get();
        $core_employees = DB::table('core_employee')->get();
        $core_companies = DB::table('core_company')->get();
        $licenseNames = LicenseName::with('licenseType')->get();
        $purposes = PurposeDetail::select('id', 'name')->get();

        return view('license.responsible', compact('responsibles', 'core_employees', 'core_companies', 'licenseNames', 'purposes'));
    }

    public function getEmployees(Request $request)
    {
        $companyId = $request->query('company_id');
        $employees = DB::table('core_employee')
            ->where('company_id', $companyId)
            ->select(
                'id',
                'emp_name',
                'emp_code',
                'emp_status',
                'emp_email',
                'emp_contact',
                'emp_department',
                'emp_designation',
                'emp_state',
                'emp_city',
                'emp_doj',
                'emp_vertical',
                'emp_region',
                'emp_zone',
                'emp_bu',
                'emp_territory'
            )
            ->get();

        return response()->json($employees);
    }

    public function getLicenseTypes()
    {
        $licenseTypes = LicenseType::select('id', 'license_type')->get();
        return response()->json($licenseTypes);
    }

    // public function getLicenseNames($licenseTypeId)
    // {
    //     $licenseNames = LicenseName::where('license_type_id', $licenseTypeId)
    //         ->select('id', 'license_name')
    //         ->get();
    //     return response()->json(['license_names' => $licenseNames]);
    // }

    public function getLicenseNames($licenseTypeId)
    {
        $responsibleId = request()->query('responsible_id', null);

        $activeLicenseIds = ResponsibleMastersLicenseDetails::whereHas('responsibleMaster', function ($query) {
                $query->where('Authorization_status', 'Active');
            })
            ->when($responsibleId, function ($query) use ($responsibleId) {
                $query->where('responsible_master_id', '!=', $responsibleId);
            })
            ->pluck('license_name_id')
            ->unique();

        $licenseNames = LicenseName::where('license_type_id', $licenseTypeId)
            ->whereNotIn('id', $activeLicenseIds)
            ->select('id', 'license_name')
            ->get();

        return response()->json(['license_names' => $licenseNames]);
    }

    public function getDraftContent($responsibleId)
    {
        try {
            $responsible = ResponsibleMaster::with(['employee', 'company'])->findOrFail($responsibleId);
            $draftId = $responsible->draft_id ?? 1; // fallback if not set
            $draft = AuthDraftMaster::findOrFail($draftId);

            $content = $draft->content;
            $replacements = [
                '[core_employee_id]'        => $responsible->employee->emp_name ?? 'N/A',
                '[emp_code]'        => $responsible->emp_code ?? 'N/A',
                '[emp_department]'  => $responsible->employee->emp_department ?? 'N/A',
                '[emp_designation]' => $responsible->employee->emp_designation ?? 'N/A',
                '[core_company_id]'    => $responsible->company->company_name ?? 'N/A',
                '[authorised_through]' => $responsible->Authorised_Through ?? '-',
                '[authorised_by]'      => $responsible->Authorisation_Issued_By ?? '-',
                '[effective_from]'     => $responsible->Effective_From ? Carbon::parse($responsible->Effective_From)->format('d-m-Y') : '-',
                '[valid_up_to]'        => ($responsible->Valid_up_to === 'Lifetime')
                    ? 'Lifetime'
                    : ($responsible->Valid_up_to ? Carbon::parse($responsible->Valid_up_to)->format('d-m-Y') : '-'),
                '[certificate_no]'     => $responsible->certificate_no ?? '-',
                '[Issue_Date]'         => $responsible->Issue_Date ? Carbon::parse($responsible->Issue_Date)->format('d-m-Y') : '-',
            ];

            foreach ($replacements as $key => $value) {
                $pattern = '/' . preg_quote($key, '/') . '(_\d+)?/';
                $content = preg_replace($pattern, $value, $content);
            }

            return response()->json(['content' => $content]);
        } catch (\Exception $e) {
            return response()->json(['content' => '', 'error' => $e->getMessage()], 404);
        }
    }

    public function getGroupcomCompanies(Request $request)    
    {   
        $companyId = $request->query('company_id');
        $groupcomCompanies = DB::table('hrm_groupcom')
            ->where('core_company_id', $companyId)
            ->select('id', 'name')
            ->get();
        return response()->json($groupcomCompanies);
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'company_id' => 'required|exists:core_company,id',
            'groupcom_company_id' => 'nullable|exists:hrm_groupcom,id',
            'emp_name' => 'required|exists:core_employee,id',
            'emp_code' => 'required|string',
            'Authorised_Through' => 'required|in:BOR,AUTHC,POA',
            'Scope_of_Authorisation' => 'nullable|string',
            'Authorisation_Issued_By' => 'nullable|string',
            'Authorised_Purpose' => 'required|array',
            'Authorised_Purpose.*' => 'exists:purpose_details,id',
            'Issue_Date' => 'nullable|date',
            'Effective_From' => 'nullable|date',
            'valid_up_to_type' => 'required|in:date,lifetime',
            'valid_up_to_date' => 'nullable|date',
            'valid_up_to_lifetime' => 'nullable|string|in:Lifetime',
            'auth_doc' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'license_category.*' => 'nullable|exists:license_types,id',
            'license_name.*' => 'nullable|exists:license_names,id',
            'purpose_details.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $licensesPurposeId = PurposeDetail::where('name', 'Licenses')->first()->id ?? null;
            if (in_array($licensesPurposeId, $validated['Authorised_Purpose']) && !empty($validated['license_name'])) {
                $activeLicenseNames = ResponsibleMastersLicenseDetails::whereIn('license_name_id', $validated['license_name'])
                    ->whereHas('responsibleMaster', function ($query) {
                        $query->where('Authorization_status', 'Active');
                    })
                    ->pluck('license_name_id')
                    ->toArray();

                if (!empty($activeLicenseNames)) {
                    $licenseNames = LicenseName::whereIn('id', $activeLicenseNames)->pluck('license_name')->toArray();
                    throw new \Exception('The following license names are already assigned to active persons: ' . implode(', ', $licenseNames));
                }
            }

            $prefixMap = [
                'BOR' => 'BOR',
                'AUTHC' => 'AUTHC',
                'POA' => 'POA',
            ];
            $prefix = $prefixMap[$validated['Authorised_Through']];
            $year = Carbon::now()->year;
            $lastRecord = ResponsibleMaster::where('certificate_no', 'LIKE', "$prefix/$year%")
                ->orderBy('certificate_no', 'desc')
                ->first();
            $serial = $lastRecord ? (int)substr($lastRecord->certificate_no, -2) + 1 : 1;
            $certificateNo = sprintf("%s/%d/%02d", $prefix, $year, $serial);

            $validUpTo = null;
            $authorizationStatus = null;

            if ($request->valid_up_to_type === 'lifetime') {
                $validUpTo = 'Lifetime';
                $authorizationStatus = 'Active';
            } elseif ($request->valid_up_to_date) {
                $validUpTo = $request->valid_up_to_date;
                $authorizationStatus = Carbon::parse($validUpTo)->gt(Carbon::now()) ? 'Active' : 'Expired';
            }

            // Handle file upload
            $authDocPath = null;
            if ($request->hasFile('auth_doc')) {
                $authDocPath = $request->file('auth_doc')->store('auth_docs', 'public');
            }

            // Store purpose_details as comma-separated IDs
            $purposeDetails = implode(',', $validated['Authorised_Purpose']);

            // Create ResponsibleMaster record
            $responsible = ResponsibleMaster::create([
                'certificate_no' => $certificateNo,
                'core_company_id' => $validated['company_id'],
                'core_groupcom_id' => $validated['groupcom_company_id'] ?? null,
                'core_employee_id' => $validated['emp_name'],
                'emp_code' => $validated['emp_code'],
                'Authorised_Through' => $validated['Authorised_Through'],
                'Scope_of_Authorisation' => $validated['Scope_of_Authorisation'],
                'Authorisation_Issued_By' => $validated['Authorisation_Issued_By'],
                'Issue_Date' => $validated['Issue_Date'],
                'Effective_From' => $validated['Effective_From'],
                'Valid_up_to' => $validUpTo,
                'Authorization_status' => $authorizationStatus,
                'auth_doc' => $authDocPath,
                'purpose_details' => $purposeDetails,
            ]);

            // Save licenses if Licenses purpose is selected
            $licensesPurposeId = PurposeDetail::where('name', 'Licenses')->first()->id ?? null;
            if (in_array($licensesPurposeId, $validated['Authorised_Purpose']) && !empty($validated['license_category'])) {
                foreach ($validated['license_category'] as $i => $licenseTypeId) {
                    if ($licenseTypeId && !empty($validated['license_name'][$i])) {
                        ResponsibleMastersLicenseDetails::create([
                            'certificate_no' => $certificateNo,
                            'responsible_master_id' => $responsible->id,
                            'license_type_id' => $licenseTypeId,
                            'license_name_id' => $validated['license_name'][$i],
                            'history_status' => $authorizationStatus,
                        ]);
                    }
                }
            }

            $authCertificate = $request->input('auth_certificate');
            if ($authCertificate) {
                $responsible->auth_certificate = $authCertificate;
            }

            DB::commit();
            return redirect()->route('responsible')->with('success', 'Responsible person added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $responsible = ResponsibleMaster::findOrFail($id);

        $validated = $request->validate([
            'company_id' => 'required|exists:core_company,id',
            'emp_name' => 'required|exists:core_employee,id',
            'emp_code' => 'required|string',
            'Authorised_Through' => 'required|in:BOR,AUTHC,POA',
            'Scope_of_Authorisation' => 'nullable|string',
            'Authorisation_Issued_By' => 'nullable|string',
            'Authorised_Purpose' => 'required|array',
            'Authorised_Purpose.*' => 'exists:purpose_details,id',
            'Issue_Date' => 'nullable|date',
            'Effective_From' => 'nullable|date',
            'valid_up_to_type' => 'required|in:date,lifetime',
            'valid_up_to_date' => 'nullable|date',
            'valid_up_to_lifetime' => 'nullable|string|in:Lifetime',
            'authorization_status_type' => 'nullable|in:revoked',
            'Revocation_Date' => 'required_if:authorization_status_type,revoked|nullable|date',
            'revocation_doc' => 'required_if:authorization_status_type,revoked|nullable|file|mimes:pdf,doc,docx|max:2048',
            'auth_doc' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'license_category.*' => 'nullable|exists:license_types,id',
            'license_name.*' => 'nullable|exists:license_names,id',
            'purpose_details.*' => 'nullable|string',
            'auth_certificate' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validUpTo = null;
            $authorizationStatus = null;
            $revocationDate = $responsible->Revocation_Date;
            $revocationDocPath = $responsible->revocation_doc;

            if ($request->filled('authorization_status_type') && $validated['authorization_status_type'] === 'revoked') {
                $authorizationStatus = 'Revoked';
                $validUpTo = $responsible->Valid_up_to;
                $revocationDate = $validated['Revocation_Date'];
                if ($request->hasFile('revocation_doc')) {
                    if ($revocationDocPath) {
                        Storage::disk('public')->delete($revocationDocPath);
                    }
                    $revocationDocPath = $request->file('revocation_doc')->store('revocation_docs', 'public');
                } elseif ($request->delete_revocation_doc == '1') {
                    if ($revocationDocPath) {
                        Storage::disk('public')->delete($revocationDocPath);
                    }
                    $revocationDocPath = null;
                }
            } elseif ($request->filled('valid_up_to_type')) {
                if ($validated['valid_up_to_type'] === 'lifetime') {
                    $validUpTo = 'Lifetime';
                    $authorizationStatus = 'Active';
                    $revocationDate = null;
                    if ($revocationDocPath) {
                        Storage::disk('public')->delete($revocationDocPath);
                    }
                    $revocationDocPath = null;
                } elseif ($request->filled('valid_up_to_date') && $validated['valid_up_to_date']) {
                    $validUpTo = $validated['valid_up_to_date'];
                    $authorizationStatus = Carbon::parse($validUpTo)->gt(Carbon::now()) ? 'Active' : 'Expired';
                    $revocationDate = null;
                    if ($revocationDocPath) {
                        Storage::disk('public')->delete($revocationDocPath);
                    }
                    $revocationDocPath = null;
                }
            }

            $authDocPath = $responsible->auth_doc;
            if ($request->hasFile('auth_doc')) {
                if ($authDocPath) {
                    Storage::disk('public')->delete($authDocPath);
                }
                $authDocPath = $request->file('auth_doc')->store('auth_docs', 'public');
            } elseif ($request->delete_auth_doc == '1') {
                if ($authDocPath) {
                    Storage::disk('public')->delete($authDocPath);
                }
                $authDocPath = null;
            }

            $authCertificate = $request->input('auth_certificate');

            $revocationDocPath = $responsible->revocation_doc;
            if ($request->hasFile('revocation_doc')) {
                if ($revocationDocPath) {
                    Storage::disk('public')->delete($revocationDocPath);
                }
                $revocationDocPath = $request->file('revocation_doc')->store('revocation_docs', 'public');
            }

            $purposeDetails = implode(',', $validated['Authorised_Purpose']);
            $responsible->update([
                'core_company_id' => $validated['company_id'],
                'core_employee_id' => $validated['emp_name'],
                'emp_code' => $validated['emp_code'],
                'Authorised_Through' => $validated['Authorised_Through'],
                'Scope_of_Authorisation' => $validated['Scope_of_Authorisation'],
                'Authorisation_Issued_By' => $validated['Authorisation_Issued_By'],
                'Issue_Date' => $validated['Issue_Date'],
                'Effective_From' => $validated['Effective_From'],
                'Valid_up_to' => $validUpTo,
                'Authorization_status' => $authorizationStatus,
                'auth_doc' => $authDocPath,
                'purpose_details' => $purposeDetails,
                'Revocation_Date' => $revocationDate,
                'revocation_doc' => $revocationDocPath,
                'auth_certificate' => $authCertificate,
            ]);

            $licensesPurposeId = PurposeDetail::where('name', 'Licenses')->first()->id ?? null;
            ResponsibleMastersLicenseDetails::where('responsible_master_id', $responsible->id)->delete();
            if (in_array($licensesPurposeId, $validated['Authorised_Purpose']) && !empty($validated['license_category'])) {
                foreach ($validated['license_category'] as $i => $licenseTypeId) {
                    if ($licenseTypeId && !empty($validated['license_name'][$i])) {
                        ResponsibleMastersLicenseDetails::create([
                            'certificate_no' => $responsible->certificate_no,
                            'responsible_master_id' => $responsible->id,
                            'license_type_id' => $licenseTypeId,
                            'license_name_id' => $validated['license_name'][$i],
                            'history_status' => $authorizationStatus,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('responsible')->with('success', 'Responsible person updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function updateCertificate(Request $request, $id)
    {
        $responsible = ResponsibleMaster::findOrFail($id);

        $validated = $request->validate([
            'auth_certificate' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $responsible->update([
                'auth_certificate' => $validated['auth_certificate'],
            ]);

            DB::commit();
            return response()->json(['message' => 'Certificate updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error updating certificate: ' . $e->getMessage()], 500);
        }
    }
}
