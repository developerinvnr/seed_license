<?php

namespace App\Http\Controllers;

use App\Models\ResponsibleMaster;
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

        // Get days filter from request (null by default to show only expired)
        $days = $request->input('days', null);

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

        // Expired authorizations (with condition to exclude if active counterpart exists)
        $expiredResponsibles = ResponsibleMaster::with(['employee', 'company', 'licenseDetails'])
            ->where('Authorization_status', 'Expired')
            ->whereIn('id', function ($query) {
                // Select the latest expired record per license_type_id and license_name_id
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
                    return true; // Show if no license details (edge case)
                }

                $licenseTypeId = $licenseDetail->license_type_id ?? null;
                $licenseNameId = $licenseDetail->license_name_id ?? null;

                if (!$licenseTypeId || !$licenseNameId) {
                    Log::warning('Expired ResponsibleMaster missing license type or name', [
                        'id' => $responsible->id,
                        'license_type_id' => $licenseTypeId,
                        'license_name_id' => $licenseNameId,
                    ]);
                    return true; // Show if license type or name is missing (edge case)
                }

                // Check if an active ResponsibleMaster exists with the same license_type and license_name
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

        // Log the alerts for debugging
        Log::info('License Alerts Fetched', [
            'days' => $days ?? 'Expired Only',
            'total_alerts' => $licenseAlerts->count(),
            'expiring_count' => $expiringLicenses->count(),
            'expired_count' => $expiredResponsibles->count(),
            'expired_ids' => $expiredResponsibles->pluck('id')->toArray(),
        ]);

        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'licenseAlerts' => $licenseAlerts->values(),
                'days' => $days ?? 'Expired Only',
            ], 200, [], JSON_UNESCAPED_SLASHES);
        }

        return view('dashboard', compact('totalLicenses', 'activeLicenses', 'deactiveLicenses', 'licenseAlerts'));
    }
}



