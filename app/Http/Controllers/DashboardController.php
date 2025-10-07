<?php

// namespace App\Http\Controllers;

// use App\Models\ResponsibleMaster;
// use App\Models\License;
// use Illuminate\Http\Request;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

// class DashboardController extends Controller
// {
//     public function index(Request $request)
//     {
//         // Counts for dashboard cards
//         $totalLicenses = ResponsibleMaster::count();
//         $activeLicenses = ResponsibleMaster::where('Authorization_status', 'Active')->count();
//         $deactiveLicenses = ResponsibleMaster::whereIn('Authorization_status', ['Expired', 'Revoked'])->count();

//         // Get days filter for License Status Alerts
//         $days = $request->input('days', null);

//         // Get date range for Recent Updates
//         $startDateFilter = $request->input('start_date_filter');
//         $endDateFilter = $request->input('end_date_filter');
//         $tabFilter = $request->input('tab_filter', 'recent-added');

//         // Validate date range
//         if ($startDateFilter && $endDateFilter && Carbon::parse($startDateFilter)->gt(Carbon::parse($endDateFilter))) {
//             Log::warning('Invalid date range: start date is after end date', [
//                 'start_date_filter' => $startDateFilter,
//                 'end_date_filter' => $endDateFilter,
//             ]);
//             $startDateFilter = null;
//             $endDateFilter = null;
//         }

//         // Build date range for Recent Updates
//         $startDate = $startDateFilter ? Carbon::parse($startDateFilter)->startOfDay() : Carbon::now()->subDays(7)->startOfDay();
//         $endDate = $endDateFilter ? Carbon::parse($endDateFilter)->endOfDay() : Carbon::now()->endOfDay();

//         // Expiring licenses (only if days filter is applied)
//         $expiringLicenses = collect([]);
//         if ($days !== null) {
//             $expiringLicenses = ResponsibleMaster::with(['employee', 'company', 'licenseDetails'])
//                 ->where('Authorization_status', 'Active')
//                 ->where('Valid_up_to', '!=', 'Lifetime')
//                 ->whereNotNull('Valid_up_to')
//                 ->where('Valid_up_to', '<=', Carbon::now()->addDays($days))
//                 ->get()
//                 ->map(function ($license) {
//                     $licenseDetail = $license->licenseDetails->first();
//                     return [
//                         'id' => $license->id,
//                         'emp_name' => optional($license->employee)->emp_name ?? 'N/A',
//                         'company_name' => optional($license->company)->company_name ?? 'N/A',
//                         'certificate_no' => $license->certificate_no ?? 'N/A',
//                         'license_type' => $licenseDetail ? ($licenseDetail->licenseType->license_type ?? 'N/A') : 'N/A',
//                         'license_name' => $licenseDetail ? ($licenseDetail->licenseName->license_name ?? 'N/A') : 'N/A',
//                         'valid_up_to' => $license->Valid_up_to ? Carbon::parse($license->Valid_up_to)->format('d-m-Y') : 'N/A',
//                         'status' => 'Expiring',
//                         'diff_for_humans' => $license->Valid_up_to ? Carbon::parse($license->Valid_up_to)->diffForHumans() : 'N/A',
//                     ];
//                 });
//         }

//         // Expired authorizations
//         $expiredResponsibles = ResponsibleMaster::with(['employee', 'company', 'licenseDetails'])
//             ->where('Authorization_status', 'Expired')
//             ->whereIn('id', function ($query) {
//                 $query->select(DB::raw('MAX(rm.id)'))
//                     ->from('responsible_masters as rm')
//                     ->join('responsible_masters_license_details as rmld', 'rm.id', '=', 'rmld.responsible_master_id')
//                     ->where('rm.Authorization_status', 'Expired')
//                     ->groupBy('rmld.license_type_id', 'rmld.license_name_id');
//             })
//             ->get()
//             ->filter(function ($responsible) {
//                 $licenseDetail = $responsible->licenseDetails->first();
//                 if (!$licenseDetail) {
//                     Log::warning('Expired ResponsibleMaster missing license details', ['id' => $responsible->id]);
//                     return true;
//                 }

//                 $licenseTypeId = $licenseDetail->license_type_id ?? null;
//                 $licenseNameId = $licenseDetail->license_name_id ?? null;

//                 if (!$licenseTypeId || !$licenseNameId) {
//                     Log::warning('Expired ResponsibleMaster missing license type or name', [
//                         'id' => $responsible->id,
//                         'license_type_id' => $licenseTypeId,
//                         'license_name_id' => $licenseNameId,
//                     ]);
//                     return true;
//                 }

//                 $hasActiveCounterpart = ResponsibleMaster::where('Authorization_status', 'Active')
//                     ->whereHas('licenseDetails', function ($query) use ($licenseTypeId, $licenseNameId) {
//                         $query->where('license_type_id', $licenseTypeId)
//                               ->where('license_name_id', $licenseNameId);
//                     })
//                     ->exists();

//                 return !$hasActiveCounterpart;
//             })
//             ->map(function ($responsible) {
//                 $licenseDetail = $responsible->licenseDetails->first();
//                 return [
//                     'id' => $responsible->id,
//                     'emp_name' => optional($responsible->employee)->emp_name ?? 'N/A',
//                     'company_name' => optional($responsible->company)->company_name ?? 'N/A',
//                     'certificate_no' => $responsible->certificate_no ?? 'N/A',
//                     'license_type' => $licenseDetail ? ($licenseDetail->licenseType->license_type ?? 'N/A') : 'N/A',
//                     'license_name' => $licenseDetail ? ($licenseDetail->licenseName->license_name ?? 'N/A') : 'N/A',
//                     'valid_up_to' => $responsible->Valid_up_to ? Carbon::parse($responsible->Valid_up_to)->format('d-m-Y') : 'N/A',
//                     'status' => 'Expired',
//                     'diff_for_humans' => null,
//                 ];
//             });

//         // Merge expiring and expired licenses
//         $licenseAlerts = $expiringLicenses->merge($expiredResponsibles)->sortBy('valid_up_to');

//         // Fetch recent added licenses
//         $recentAdded = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
//             ->whereBetween('created_at', [$startDate, $endDate])
//             ->orderBy('created_at', 'desc')
//             ->take(10)
//             ->get()
//             ->map(function ($license) use ($startDate, $endDate) {
//                 return [
//                     'id' => $license->id,
//                     'company_name' => optional($license->company)->company_name ?? 'N/A',
//                     'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
//                     'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
//                     'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
//                     'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
//                     'status' => $license->lis_status,
//                     'activity_type' => 'Created',
//                     'activity_date' => Carbon::parse($license->created_at)->diffForHumans(),
//                     'created_at' => $license->created_at->format('Y-m-d H:i'),
//                     'is_within_date_range' => $license->created_at->between($startDate, $endDate),
//                 ];
//             });

//         // Fetch recent modified licenses
//         $recentModified = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
//             ->where(function ($query) use ($startDate, $endDate) {
//                 $query->whereBetween('updated_at', [$startDate, $endDate])
//                       ->orWhere(function ($subQuery) use ($startDate, $endDate) {
//                           $subQuery->where('license_performance', 'modification')
//                                    ->whereBetween('updated_at', [$startDate, $endDate]);
//                       });
//             })
//             ->where('created_at', '<', $startDate)
//             ->orderBy('updated_at', 'desc')
//             ->take(10)
//             ->get()
//             ->map(function ($license) use ($startDate, $endDate) {
//                 return [
//                     'id' => $license->id,
//                     'company_name' => optional($license->company)->company_name ?? 'N/A',
//                     'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
//                     'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
//                     'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
//                     'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
//                     'status' => $license->lis_status,
//                     'activity_type' => 'Modified',
//                     'activity_date' => Carbon::parse($license->updated_at)->diffForHumans(),
//                     'updated_at' => $license->updated_at->format('Y-m-d H:i'),
//                     'is_within_date_range' => $license->updated_at->between($startDate, $endDate) || ($license->license_performance === 'modification' && $license->updated_at->between($startDate, $endDate)),
//                 ];
//             });

//         // Fetch approved licenses
//         $approvedLicenses = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
//             ->where('application_status', 'Approved')
//             ->whereBetween('updated_at', [$startDate, $endDate])
//             ->orderBy('updated_at', 'desc')
//             ->take(10)
//             ->get()
//             ->map(function ($license) use ($startDate, $endDate) {
//                 return [
//                     'id' => $license->id,
//                     'company_name' => optional($license->company)->company_name ?? 'N/A',
//                     'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
//                     'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
//                     'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
//                     'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
//                     'status' => $license->lis_status,
//                     'activity_type' => 'Approved',
//                     'activity_date' => Carbon::parse($license->updated_at)->diffForHumans(),
//                     'updated_at' => $license->updated_at->format('Y-m-d H:i'),
//                     'is_within_date_range' => $license->updated_at->between($startDate, $endDate),
//                 ];
//             });

//         // Fetch rejected licenses
//         $rejectedLicenses = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
//             ->where('application_status', 'Rejected')
//             ->whereBetween('updated_at', [$startDate, $endDate])
//             ->orderBy('updated_at', 'desc')
//             ->take(10)
//             ->get()
//             ->map(function ($license) use ($startDate, $endDate) {
//                 return [
//                     'id' => $license->id,
//                     'company_name' => optional($license->company)->company_name ?? 'N/A',
//                     'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
//                     'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
//                     'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
//                     'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
//                     'status' => $license->lis_status,
//                     'activity_type' => 'Rejected',
//                     'activity_date' => Carbon::parse($license->updated_at)->diffForHumans(),
//                     'updated_at' => $license->updated_at->format('Y-m-d H:i'),
//                     'is_within_date_range' => $license->updated_at->between($startDate, $endDate),
//                 ];
//             });

//         // Log for debugging
//         Log::info('Dashboard Data Fetched', [
//             'days' => $days ?? 'Expired Only',
//             'tab_filter' => $tabFilter,
//             'date_range' => [
//                 'start_date_filter' => $startDateFilter ?? 'Default (7 days)',
//                 'end_date_filter' => $endDateFilter ?? 'Today',
//                 'start_date' => $startDate->toDateTimeString(),
//                 'end_date' => $endDate->toDateTimeString(),
//             ],
//             'total_alerts' => $licenseAlerts->count(),
//             'expiring_count' => $expiringLicenses->count(),
//             'expired_count' => $expiredResponsibles->count(),
//             'recent_added_count' => $recentAdded->count(),
//             'recent_added_ids' => $recentAdded->pluck('id')->toArray(),
//             'recent_added_dates' => $recentAdded->pluck('created_at')->toArray(),
//             'recent_modified_count' => $recentModified->count(),
//             'recent_modified_ids' => $recentModified->pluck('id')->toArray(),
//             'recent_modified_dates' => $recentModified->pluck('updated_at')->toArray(),
//             'approved_count' => $approvedLicenses->count(),
//             'approved_ids' => $approvedLicenses->pluck('id')->toArray(),
//             'approved_dates' => $approvedLicenses->pluck('updated_at')->toArray(),
//             'rejected_count' => $rejectedLicenses->count(),
//             'rejected_ids' => $rejectedLicenses->pluck('id')->toArray(),
//             'rejected_dates' => $rejectedLicenses->pluck('updated_at')->toArray(),
//         ]);

//         // Return JSON for AJAX requests
//         if ($request->ajax()) {
//             return response()->json([
//                 'licenseAlerts' => $licenseAlerts->values(),
//                 'recentAdded' => $recentAdded->values(),
//                 'recentModified' => $recentModified->values(),
//                 'approvedLicenses' => $approvedLicenses->values(),
//                 'rejectedLicenses' => $rejectedLicenses->values(),
//             ], 200, [], JSON_UNESCAPED_SLASHES);
//         }

//         return view('dashboard', compact('totalLicenses', 'activeLicenses', 'deactiveLicenses', 'licenseAlerts', 'recentAdded', 'recentModified', 'approvedLicenses', 'rejectedLicenses', 'startDateFilter', 'endDateFilter'));
//     }
// }



namespace App\Http\Controllers;

use App\Models\ResponsibleMaster;
use App\Models\License;
use App\Models\LicenseType;
use App\Models\CoreState;
use App\Models\CoreEmployee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Counts for dashboard cards
        $totalLicenses = ResponsibleMaster::count();
        $activeLicenses = ResponsibleMaster::where('Authorization_status', 'Active')->count();
        $deactiveLicenses = ResponsibleMaster::whereIn('Authorization_status', ['Expired', 'Revoked'])->count();

        // Get days filter for License Status Alerts
        $days = $request->input('days', null);

        // Get date range for Recent Updates
        $startDateFilter = $request->input('start_date_filter');
        $endDateFilter = $request->input('end_date_filter');
        $tabFilter = $request->input('tab_filter', 'recent-added'); // Ensure default value

        // Validate date range
        if ($startDateFilter && $endDateFilter && Carbon::parse($startDateFilter)->gt(Carbon::parse($endDateFilter))) {
            Log::warning('Invalid date range: start date is after end date', [
                'start_date_filter' => $startDateFilter,
                'end_date_filter' => $endDateFilter,
            ]);
            $startDateFilter = null;
            $endDateFilter = null;
        }

        // Build date range for Recent Updates
        $startDate = $startDateFilter ? Carbon::parse($startDateFilter)->startOfDay() : Carbon::now()->subDays(7)->startOfDay();
        $endDate = $endDateFilter ? Carbon::parse($endDateFilter)->endOfDay() : Carbon::now()->endOfDay();

        // Expiring licenses (only if days filter is applied)
        $expiringLicenses = collect([]);
        if ($days !== null) {
            $expiringLicenses = ResponsibleMaster::with(['employee', 'company', 'licenseDetails'])
                ->where('Authorization_status', 'Active')
                ->where('Valid_up_to', '!=', 'Lifetime')
                ->whereNotNull('Valid_up_to')
                ->where('Valid_up_to', '<=', Carbon::now()->addDays($days))
                ->get()
                ->map(function ($license) {
                    $licenseDetail = $license->licenseDetails->first();
                    return [
                        'id' => $license->id,
                        'emp_name' => optional($license->employee)->emp_name ?? 'N/A',
                        'company_name' => optional($license->company)->company_name ?? 'N/A',
                        'certificate_no' => $license->certificate_no ?? 'N/A',
                        'license_type' => $licenseDetail ? ($licenseDetail->licenseType->license_type ?? 'N/A') : 'N/A',
                        'license_name' => $licenseDetail ? ($licenseDetail->licenseName->license_name ?? 'N/A') : 'N/A',
                        'valid_up_to' => $license->Valid_up_to ? Carbon::parse($license->Valid_up_to)->format('d-m-Y') : 'N/A',
                        'status' => 'Expiring',
                        'diff_for_humans' => $license->Valid_up_to ? Carbon::parse($license->Valid_up_to)->diffForHumans() : 'N/A',
                    ];
                });
        }

        // Expired authorizations
        $expiredResponsibles = ResponsibleMaster::with(['employee', 'company', 'licenseDetails'])
            ->where('Authorization_status', 'Expired')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(rm.id)'))
                    ->from('responsible_masters as rm')
                    ->join('responsible_masters_license_details as rmld', 'rm.id', '=', 'rmld.responsible_master_id')
                    ->where('rm.Authorization_status', 'Expired')
                    ->groupBy('rmld.license_type_id', 'rmld.license_name_id');
            })
            ->get()
            ->filter(function ($responsible) {
                $licenseDetail = $responsible->licenseDetails->first();
                if (!$licenseDetail) {
                    Log::warning('Expired ResponsibleMaster missing license details', ['id' => $responsible->id]);
                    return true;
                }

                $licenseTypeId = $licenseDetail->license_type_id ?? null;
                $licenseNameId = $licenseDetail->license_name_id ?? null;

                if (!$licenseTypeId || !$licenseNameId) {
                    Log::warning('Expired ResponsibleMaster missing license type or name', [
                        'id' => $responsible->id,
                        'license_type_id' => $licenseTypeId,
                        'license_name_id' => $licenseNameId,
                    ]);
                    return true;
                }

                $hasActiveCounterpart = ResponsibleMaster::where('Authorization_status', 'Active')
                    ->whereHas('licenseDetails', function ($query) use ($licenseTypeId, $licenseNameId) {
                        $query->where('license_type_id', $licenseTypeId)
                              ->where('license_name_id', $licenseNameId);
                    })
                    ->exists();

                return !$hasActiveCounterpart;
            })
            ->map(function ($responsible) {
                $licenseDetail = $responsible->licenseDetails->first();
                return [
                    'id' => $responsible->id,
                    'emp_name' => optional($responsible->employee)->emp_name ?? 'N/A',
                    'company_name' => optional($responsible->company)->company_name ?? 'N/A',
                    'certificate_no' => $responsible->certificate_no ?? 'N/A',
                    'license_type' => $licenseDetail ? ($licenseDetail->licenseType->license_type ?? 'N/A') : 'N/A',
                    'license_name' => $licenseDetail ? ($licenseDetail->licenseName->license_name ?? 'N/A') : 'N/A',
                    'valid_up_to' => $responsible->Valid_up_to ? Carbon::parse($responsible->Valid_up_to)->format('d-m-Y') : 'N/A',
                    'status' => 'Expired',
                    'diff_for_humans' => null,
                ];
            });

        // Merge expiring and expired licenses
        $licenseAlerts = $expiringLicenses->merge($expiredResponsibles)->sortBy('valid_up_to');

        // Fetch recent added licenses
        $recentAdded = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($license) use ($startDate, $endDate) {
                return [
                    'id' => $license->id,
                    'company_name' => optional($license->company)->company_name ?? 'N/A',
                    'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
                    'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
                    'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
                    'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
                    'status' => $license->lis_status,
                    'activity_type' => 'Created',
                    'activity_date' => Carbon::parse($license->created_at)->diffForHumans(),
                    'created_at' => $license->created_at->format('Y-m-d H:i'),
                    'is_within_date_range' => $license->created_at->between($startDate, $endDate),
                ];
            });

        // Fetch recent modified licenses
        $recentModified = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('updated_at', [$startDate, $endDate])
                      ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                          $subQuery->where('license_performance', 'modification')
                                   ->whereBetween('updated_at', [$startDate, $endDate]);
                      });
            })
            ->where('created_at', '<', $startDate)
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($license) use ($startDate, $endDate) {
                return [
                    'id' => $license->id,
                    'company_name' => optional($license->company)->company_name ?? 'N/A',
                    'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
                    'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
                    'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
                    'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
                    'status' => $license->lis_status,
                    'activity_type' => 'Modified',
                    'activity_date' => Carbon::parse($license->updated_at)->diffForHumans(),
                    'updated_at' => $license->updated_at->format('Y-m-d H:i'),
                    'is_within_date_range' => $license->updated_at->between($startDate, $endDate) || ($license->license_performance === 'modification' && $license->updated_at->between($startDate, $endDate)),
                ];
            });

        // Fetch approved licenses
        $approvedLicenses = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
            ->where('application_status', 'Approved')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($license) use ($startDate, $endDate) {
                return [
                    'id' => $license->id,
                    'company_name' => optional($license->company)->company_name ?? 'N/A',
                    'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
                    'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
                    'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
                    'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
                    'status' => $license->lis_status,
                    'activity_type' => 'Approved',
                    'activity_date' => Carbon::parse($license->updated_at)->diffForHumans(),
                    'updated_at' => $license->updated_at->format('Y-m-d H:i'),
                    'is_within_date_range' => $license->updated_at->between($startDate, $endDate),
                ];
            });

        // Fetch rejected licenses
        $rejectedLicenses = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
            ->where('application_status', 'Rejected')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($license) use ($startDate, $endDate) {
                return [
                    'id' => $license->id,
                    'company_name' => optional($license->company)->company_name ?? 'N/A',
                    'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
                    'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
                    'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
                    'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
                    'status' => $license->lis_status,
                    'activity_type' => 'Rejected',
                    'activity_date' => Carbon::parse($license->updated_at)->diffForHumans(),
                    'updated_at' => $license->updated_at->format('Y-m-d H:i'),
                    'is_within_date_range' => $license->updated_at->between($startDate, $endDate),
                ];
            });

        // Fetch upcoming license expiry alerts
        $expiryDays = $request->input('expiry_days', 30); // Default to 30 days
        $stateId = $request->input('state_id');
        $licenseTypeId = $request->input('license_type_id');
        $responsiblePersonId = $request->input('responsible_person_id');

        $upcomingExpiries = License::with(['licenseType', 'licenseName', 'company', 'groupcom', 'state'])
            ->where('lis_status', 'Active')
            ->whereNotNull('valid_upto')
            ->whereBetween('valid_upto', [Carbon::now(), Carbon::now()->addDays($expiryDays)])
            ->when($stateId, function ($query, $stateId) {
                return $query->where('state_id', $stateId);
            })
            ->when($licenseTypeId, function ($query, $licenseTypeId) {
                return $query->where('license_type_id', $licenseTypeId);
            })
            ->when($responsiblePersonId, function ($query, $responsiblePersonId) {
                return $query->where('responsible_person', $responsiblePersonId);
            })
            ->get()
            ->map(function ($license) {
                $validUpto = Carbon::parse($license->valid_upto);
                $daysUntilExpiry = $validUpto->diffInDays(Carbon::now());
                $expiryCategory = $daysUntilExpiry <= 30 ? '30 Days' : ($daysUntilExpiry <= 60 ? '60 Days' : '90 Days');
                return [
                    'id' => $license->id,
                    'company_name' => optional($license->company)->company_name ?? 'N/A',
                    'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
                    'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
                    'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
                    'valid_upto' => $validUpto->format('d-m-Y'),
                    'state_name' => optional($license->state)->state_name ?? 'N/A',
                    'responsible_person_name' => optional($license->employee)->emp_name ?? 'N/A',
                    'days_until_expiry' => $daysUntilExpiry,
                    'expiry_category' => $expiryCategory,
                    'diff_for_humans' => $validUpto->diffForHumans(),
                ];
            })
            ->sortBy('valid_upto');

        // Fetch states, license types, and responsible persons for filters
        $states = CoreState::where('country_id', 1)->get(['id', 'state_name']);
        $licenseTypes = LicenseType::all(['id', 'license_type']);
        $responsiblePersons = CoreEmployee::join('licenses', 'core_employee.id', '=', 'licenses.responsible_person')
            ->select('core_employee.id', 'core_employee.emp_name')
            ->distinct()
            ->get();

        // Log for debugging
        Log::info('Dashboard Data Fetched', [
            'days' => $days ?? 'Expired Only',
            'expiry_days' => $expiryDays,
            'tab_filter' => $tabFilter,
            'date_range' => [
                'start_date_filter' => $startDateFilter ?? 'Default (7 days)',
                'end_date_filter' => $endDateFilter ?? 'Today',
                'start_date' => $startDate->toDateTimeString(),
                'end_date' => $endDate->toDateTimeString(),
            ],
            'total_alerts' => $licenseAlerts->count(),
            'expiring_count' => $expiringLicenses->count(),
            'expired_count' => $expiredResponsibles->count(),
            'upcoming_expiries_count' => $upcomingExpiries->count(),
            'recent_added_count' => $recentAdded->count(),
            'recent_added_ids' => $recentAdded->pluck('id')->toArray(),
            'recent_added_dates' => $recentAdded->pluck('created_at')->toArray(),
            'recent_modified_count' => $recentModified->count(),
            'recent_modified_ids' => $recentModified->pluck('id')->toArray(),
            'recent_modified_dates' => $recentModified->pluck('updated_at')->toArray(),
            'approved_count' => $approvedLicenses->count(),
            'approved_ids' => $approvedLicenses->pluck('id')->toArray(),
            'approved_dates' => $approvedLicenses->pluck('updated_at')->toArray(),
            'rejected_count' => $rejectedLicenses->count(),
            'rejected_ids' => $rejectedLicenses->pluck('id')->toArray(),
            'rejected_dates' => $rejectedLicenses->pluck('updated_at')->toArray(),
        ]);

        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'licenseAlerts' => $licenseAlerts->values(),
                'recentAdded' => $recentAdded->values(),
                'recentModified' => $recentModified->values(),
                'approvedLicenses' => $approvedLicenses->values(),
                'rejectedLicenses' => $rejectedLicenses->values(),
                'upcomingExpiries' => $upcomingExpiries->values(),
                'states' => $states,
                'licenseTypes' => $licenseTypes,
                'responsiblePersons' => $responsiblePersons,
            ], 200, [], JSON_UNESCAPED_SLASHES);
        }

        // Pass $tabFilter to the view
        return view('dashboard', compact(
            'totalLicenses',
            'activeLicenses',
            'deactiveLicenses',
            'licenseAlerts',
            'recentAdded',
            'recentModified',
            'approvedLicenses',
            'rejectedLicenses',
            'upcomingExpiries',
            'states',
            'licenseTypes',
            'responsiblePersons',
            'startDateFilter',
            'endDateFilter',
            'tabFilter' // Added to compact
        ));
    }
}


// namespace App\Http\Controllers;

// use App\Models\ResponsibleMaster;
// use App\Models\License;
// use App\Models\CoreState;
// use App\Models\LicenseType;
// use App\Models\CoreEmployee;
// use Illuminate\Http\Request;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

// class DashboardController extends Controller
// {
//     public function index(Request $request)
//     {
//         // Counts for dashboard cards
//         $totalLicenses = ResponsibleMaster::count();
//         $activeLicenses = ResponsibleMaster::where('Authorization_status', 'Active')->count();
//         $deactiveLicenses = ResponsibleMaster::whereIn('Authorization_status', ['Expired', 'Revoked'])->count();

//         // Get days filter for License Status Alerts
//         $days = $request->input('days', null);

//         // Get date range for Recent Updates
//         $startDateFilter = $request->input('start_date_filter');
//         $endDateFilter = $request->input('end_date_filter');
//         $tabFilter = $request->input('tab_filter', 'recent-added');

//         // Validate date range
//         if ($startDateFilter && $endDateFilter && Carbon::parse($startDateFilter)->gt(Carbon::parse($endDateFilter))) {
//             Log::warning('Invalid date range: start date is after end date', [
//                 'start_date_filter' => $startDateFilter,
//                 'end_date_filter' => $endDateFilter,
//             ]);
//             $startDateFilter = null;
//             $endDateFilter = null;
//         }

//         // Build date range for Recent Updates
//         $startDate = $startDateFilter ? Carbon::parse($startDateFilter)->startOfDay() : Carbon::now()->subDays(7)->startOfDay();
//         $endDate = $endDateFilter ? Carbon::parse($endDateFilter)->endOfDay() : Carbon::now()->endOfDay();

//         // Expiring licenses (only if days filter is applied)
//         $expiringLicenses = collect([]);
//         if ($days !== null) {
//             $expiringLicenses = ResponsibleMaster::with(['employee', 'company', 'licenseDetails'])
//                 ->where('Authorization_status', 'Active')
//                 ->where('Valid_up_to', '!=', 'Lifetime')
//                 ->whereNotNull('Valid_up_to')
//                 ->where('Valid_up_to', '<=', Carbon::now()->addDays($days))
//                 ->get()
//                 ->map(function ($license) {
//                     $licenseDetail = $license->licenseDetails->first();
//                     return [
//                         'id' => $license->id,
//                         'emp_name' => optional($license->employee)->emp_name ?? 'N/A',
//                         'company_name' => optional($license->company)->company_name ?? 'N/A',
//                         'certificate_no' => $license->certificate_no ?? 'N/A',
//                         'license_type' => $licenseDetail ? ($licenseDetail->licenseType->license_type ?? 'N/A') : 'N/A',
//                         'license_name' => $licenseDetail ? ($licenseDetail->licenseName->license_name ?? 'N/A') : 'N/A',
//                         'valid_up_to' => $license->Valid_up_to ? Carbon::parse($license->Valid_up_to)->format('d-m-Y') : 'N/A',
//                         'status' => 'Expiring',
//                         'diff_for_humans' => $license->Valid_up_to ? Carbon::parse($license->Valid_up_to)->diffForHumans() : 'N/A',
//                     ];
//                 });
//         }

//         // Expired authorizations
//         $expiredResponsibles = ResponsibleMaster::with(['employee', 'company', 'licenseDetails'])
//             ->where('Authorization_status', 'Expired')
//             ->whereIn('id', function ($query) {
//                 $query->select(DB::raw('MAX(rm.id)'))
//                     ->from('responsible_masters as rm')
//                     ->join('responsible_masters_license_details as rmld', 'rm.id', '=', 'rmld.responsible_master_id')
//                     ->where('rm.Authorization_status', 'Expired')
//                     ->groupBy('rmld.license_type_id', 'rmld.license_name_id');
//             })
//             ->get()
//             ->filter(function ($responsible) {
//                 $licenseDetail = $responsible->licenseDetails->first();
//                 if (!$licenseDetail) {
//                     Log::warning('Expired ResponsibleMaster missing license details', ['id' => $responsible->id]);
//                     return true;
//                 }

//                 $licenseTypeId = $licenseDetail->license_type_id ?? null;
//                 $licenseNameId = $licenseDetail->license_name_id ?? null;

//                 if (!$licenseTypeId || !$licenseNameId) {
//                     Log::warning('Expired ResponsibleMaster missing license type or name', [
//                         'id' => $responsible->id,
//                         'license_type_id' => $licenseTypeId,
//                         'license_name_id' => $licenseNameId,
//                     ]);
//                     return true;
//                 }

//                 $hasActiveCounterpart = ResponsibleMaster::where('Authorization_status', 'Active')
//                     ->whereHas('licenseDetails', function ($query) use ($licenseTypeId, $licenseNameId) {
//                         $query->where('license_type_id', $licenseTypeId)
//                               ->where('license_name_id', $licenseNameId);
//                     })
//                     ->exists();

//                 return !$hasActiveCounterpart;
//             })
//             ->map(function ($responsible) {
//                 $licenseDetail = $responsible->licenseDetails->first();
//                 return [
//                     'id' => $responsible->id,
//                     'emp_name' => optional($responsible->employee)->emp_name ?? 'N/A',
//                     'company_name' => optional($responsible->company)->company_name ?? 'N/A',
//                     'certificate_no' => $responsible->certificate_no ?? 'N/A',
//                     'license_type' => $licenseDetail ? ($licenseDetail->licenseType->license_type ?? 'N/A') : 'N/A',
//                     'license_name' => $licenseDetail ? ($licenseDetail->licenseName->license_name ?? 'N/A') : 'N/A',
//                     'valid_up_to' => $responsible->Valid_up_to ? Carbon::parse($responsible->Valid_up_to)->format('d-m-Y') : 'N/A',
//                     'status' => 'Expired',
//                     'diff_for_humans' => null,
//                 ];
//             });

//         // Merge expiring and expired licenses
//         $licenseAlerts = $expiringLicenses->merge($expiredResponsibles)->sortBy('valid_up_to');

//         // Fetch recent added licenses
//         $recentAdded = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
//             ->whereBetween('created_at', [$startDate, $endDate])
//             ->orderBy('created_at', 'desc')
//             ->take(10)
//             ->get()
//             ->map(function ($license) use ($startDate, $endDate) {
//                 return [
//                     'id' => $license->id,
//                     'company_name' => optional($license->company)->company_name ?? 'N/A',
//                     'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
//                     'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
//                     'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
//                     'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
//                     'status' => $license->lis_status,
//                     'activity_type' => 'Created',
//                     'activity_date' => Carbon::parse($license->created_at)->diffForHumans(),
//                     'created_at' => $license->created_at->format('Y-m-d H:i'),
//                     'is_within_date_range' => $license->created_at->between($startDate, $endDate),
//                 ];
//             });

//         // Fetch recent modified licenses
//         $recentModified = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
//             ->where(function ($query) use ($startDate, $endDate) {
//                 $query->whereBetween('updated_at', [$startDate, $endDate])
//                       ->orWhere(function ($subQuery) use ($startDate, $endDate) {
//                           $subQuery->where('license_performance', 'modification')
//                                    ->whereBetween('updated_at', [$startDate, $endDate]);
//                       });
//             })
//             ->where('created_at', '<', $startDate)
//             ->orderBy('updated_at', 'desc')
//             ->take(10)
//             ->get()
//             ->map(function ($license) use ($startDate, $endDate) {
//                 return [
//                     'id' => $license->id,
//                     'company_name' => optional($license->company)->company_name ?? 'N/A',
//                     'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
//                     'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
//                     'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
//                     'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
//                     'status' => $license->lis_status,
//                     'activity_type' => 'Modified',
//                     'activity_date' => Carbon::parse($license->updated_at)->diffForHumans(),
//                     'updated_at' => $license->updated_at->format('Y-m-d H:i'),
//                     'is_within_date_range' => $license->updated_at->between($startDate, $endDate) || ($license->license_performance === 'modification' && $license->updated_at->between($startDate, $endDate)),
//                 ];
//             });

//         // Fetch approved licenses
//         $approvedLicenses = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
//             ->where('application_status', 'Approved')
//             ->whereBetween('updated_at', [$startDate, $endDate])
//             ->orderBy('updated_at', 'desc')
//             ->take(10)
//             ->get()
//             ->map(function ($license) use ($startDate, $endDate) {
//                 return [
//                     'id' => $license->id,
//                     'company_name' => optional($license->company)->company_name ?? 'N/A',
//                     'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
//                     'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
//                     'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
//                     'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
//                     'status' => $license->lis_status,
//                     'activity_type' => 'Approved',
//                     'activity_date' => Carbon::parse($license->updated_at)->diffForHumans(),
//                     'updated_at' => $license->updated_at->format('Y-m-d H:i'),
//                     'is_within_date_range' => $license->updated_at->between($startDate, $endDate),
//                 ];
//             });

//         // Fetch rejected licenses
//         $rejectedLicenses = License::with(['licenseType', 'licenseName', 'company', 'groupcom'])
//             ->where('application_status', 'Rejected')
//             ->whereBetween('updated_at', [$startDate, $endDate])
//             ->orderBy('updated_at', 'desc')
//             ->take(10)
//             ->get()
//             ->map(function ($license) use ($startDate, $endDate) {
//                 return [
//                     'id' => $license->id,
//                     'company_name' => optional($license->company)->company_name ?? 'N/A',
//                     'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
//                     'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
//                     'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
//                     'valid_upto' => $license->valid_upto ? Carbon::parse($license->valid_upto)->format('d-m-Y') : 'N/A',
//                     'status' => $license->lis_status,
//                     'activity_type' => 'Rejected',
//                     'activity_date' => Carbon::parse($license->updated_at)->diffForHumans(),
//                     'updated_at' => $license->updated_at->format('Y-m-d H:i'),
//                     'is_within_date_range' => $license->updated_at->between($startDate, $endDate),
//                 ];
//             });

//         // Fetch upcoming license expiry alerts
//         $expiryDays = $request->input('expiry_days', 30); // Default to 30 days
//         $expiryStart = $request->input('expiry_start');
//         $expiryEnd = $request->input('expiry_end');
//         $stateId = $request->input('state_id');
//         $licenseTypeId = $request->input('license_type_id');
//         $responsiblePersonId = $request->input('responsible_person_id');

//         $query = License::with(['licenseType', 'licenseName', 'company', 'groupcom', 'state', 'employee'])
//             ->where('lis_status', 'Active')
//             ->whereNotNull('valid_upto');

//         if ($expiryStart && $expiryEnd) {
//             $expiryStartDate = Carbon::parse($expiryStart)->startOfDay();
//             $expiryEndDate = Carbon::parse($expiryEnd)->endOfDay();
//             $query->whereBetween('valid_upto', [$expiryStartDate, $expiryEndDate]);
//         } else {
//             $query->whereBetween('valid_upto', [Carbon::now(), Carbon::now()->addDays($expiryDays)]);
//         }

//         $query->when($stateId, function ($query, $stateId) {
//             return $query->where('state_id', $stateId);
//         })
//         ->when($licenseTypeId, function ($query, $licenseTypeId) {
//             return $query->where('license_type_id', $licenseTypeId);
//         })
//         ->when($responsiblePersonId, function ($query, $responsiblePersonId) {
//             return $query->where('responsible_person', $responsiblePersonId);
//         });

//         $upcomingExpiries = $query->get()
//             ->map(function ($license) {
//                 $validUpto = Carbon::parse($license->valid_upto);
//                 $daysUntilExpiry = $validUpto->diffInDays(Carbon::now());
//                 $expiryCategory = $daysUntilExpiry <= 30 ? '30 Days' : ($daysUntilExpiry <= 60 ? '60 Days' : '90 Days');
//                 return [
//                     'id' => $license->id,
//                     'company_name' => optional($license->company)->company_name ?? 'N/A',
//                     'groupcom_name' => optional($license->groupcom)->name ?? (optional($license->company)->company_name ?? 'N/A'),
//                     'license_type' => optional($license->licenseType)->license_type ?? 'N/A',
//                     'license_name' => optional($license->licenseName)->license_name ?? 'N/A',
//                     'valid_upto' => $validUpto->format('d-m-Y'),
//                     'state_name' => optional($license->state)->state_name ?? 'N/A',
//                     'responsible_person_name' => optional($license->employee)->emp_name ?? 'N/A',
//                     'days_until_expiry' => $daysUntilExpiry,
//                     'expiry_category' => $expiryCategory,
//                     'diff_for_humans' => $validUpto->diffForHumans(),
//                 ];
//             })
//             ->sortBy('valid_upto');

//         // Fetch states, license types, and responsible persons for filters
//         $states = CoreState::where('country_id', 1)->get(['id', 'state_name']);
//         $licenseTypes = LicenseType::all(['id', 'license_type']);
//         $responsiblePersons = CoreEmployee::join('licenses', 'core_employee.id', '=', 'licenses.responsible_person')
//             ->select('core_employee.id', 'core_employee.emp_name')
//             ->distinct()
//             ->get();

//         // Log for debugging
//         Log::info('Dashboard Data Fetched', [
//             'days' => $days ?? 'Expired Only',
//             'expiry_days' => $expiryDays,
//             'expiry_start' => $expiryStart,
//             'expiry_end' => $expiryEnd,
//             'tab_filter' => $tabFilter,
//             'date_range' => [
//                 'start_date_filter' => $startDateFilter ?? 'Default (7 days)',
//                 'end_date_filter' => $endDateFilter ?? 'Today',
//                 'start_date' => $startDate->toDateTimeString(),
//                 'end_date' => $endDate->toDateTimeString(),
//             ],
//             'total_alerts' => $licenseAlerts->count(),
//             'expiring_count' => $expiringLicenses->count(),
//             'expired_count' => $expiredResponsibles->count(),
//             'upcoming_expiries_count' => $upcomingExpiries->count(),
//             'recent_added_count' => $recentAdded->count(),
//             'recent_added_ids' => $recentAdded->pluck('id')->toArray(),
//             'recent_added_dates' => $recentAdded->pluck('created_at')->toArray(),
//             'recent_modified_count' => $recentModified->count(),
//             'recent_modified_ids' => $recentModified->pluck('id')->toArray(),
//             'recent_modified_dates' => $recentModified->pluck('updated_at')->toArray(),
//             'approved_count' => $approvedLicenses->count(),
//             'approved_ids' => $approvedLicenses->pluck('id')->toArray(),
//             'approved_dates' => $approvedLicenses->pluck('updated_at')->toArray(),
//             'rejected_count' => $rejectedLicenses->count(),
//             'rejected_ids' => $rejectedLicenses->pluck('id')->toArray(),
//             'rejected_dates' => $rejectedLicenses->pluck('updated_at')->toArray(),
//         ]);

//         // Return JSON for AJAX requests
//         if ($request->ajax()) {
//             return response()->json([
//                 'licenseAlerts' => $licenseAlerts->values(),
//                 'recentAdded' => $recentAdded->values(),
//                 'recentModified' => $recentModified->values(),
//                 'approvedLicenses' => $approvedLicenses->values(),
//                 'rejectedLicenses' => $rejectedLicenses->values(),
//                 'upcomingExpiries' => $upcomingExpiries->values(),
//                 'states' => $states,
//                 'licenseTypes' => $licenseTypes,
//                 'responsiblePersons' => $responsiblePersons,
//             ], 200, [], JSON_UNESCAPED_SLASHES);
//         }

//         // Pass $tabFilter to the view
//         return view('dashboard', compact(
//             'totalLicenses',
//             'activeLicenses',
//             'deactiveLicenses',
//             'licenseAlerts',
//             'recentAdded',
//             'recentModified',
//             'approvedLicenses',
//             'rejectedLicenses',
//             'upcomingExpiries',
//             'states',
//             'licenseTypes',
//             'responsiblePersons',
//             'startDateFilter',
//             'endDateFilter',
//             'tabFilter'
//         ));
//     }
// }