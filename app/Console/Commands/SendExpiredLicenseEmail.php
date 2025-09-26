<?php
// namespace App\Console\Commands;
// use App\Models\ResponsibleMaster;
// use App\Models\User;
// use App\Models\CoreEmployee;
// use Carbon\Carbon;
// use Illuminate\Console\Command;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Mail;

// class SendExpiredLicenseEmail extends Command
// {
//     protected $signature = 'email:expired-licenses';
//     protected $description = 'Send email notifications about expired licenses to authorized users, employees, and their managers';

//     public function handle()
//     {
//         $permission = \Spatie\Permission\Models\Permission::where('name', 'view-company Responsible Person')->first();
//         if (!$permission) {
//             $this->error('Permission "view-company Responsible Person" not found.');
//             Log::error('Permission not found', ['name' => 'view-company Responsible Person']);
//             return 1;
//         }
//         $this->info('Permission found: ' . $permission->name);

//         $users = User::whereHas('permissions', function ($query) {
//             $query->where('name', 'view-company Responsible Person');
//         })->orWhereHas('roles.permissions', function ($query) {
//             $query->where('name', 'view-company Responsible Person');
//         })->get(['id', 'name', 'email']);

//         if ($users->isEmpty()) {
//             $this->info('No users found with view-company Responsible Person permission.');
//         } else {
//             $this->info('Found ' . $users->count() . ' users with permission:');
//             foreach ($users as $user) {
//                 $this->info("User: {$user->name} ({$user->email})");
//             }
//         }

//         $expiredLicensesRaw = ResponsibleMaster::with([
//             'employee' => function ($query) {
//                 $query->select('id', 'emp_name', 'emp_email', 'emp_reporting');
//             },
//             'company' => function ($query) {
//                 $query->select('id', 'company_name');
//             },
//             'licenseDetails.licenseType' => function ($query) {
//                 $query->select('id', 'license_type');
//             },
//             'licenseDetails.licenseName' => function ($query) {
//                 $query->select('id', 'license_name');
//             }
//         ])
//             ->where('Authorization_status', 'Expired')
//             ->whereIn('id', function ($query) {
//                 $query->select(DB::raw('MAX(rm.id)'))
//                     ->from('responsible_masters as rm')
//                     ->join('responsible_masters_license_details as rmld', 'rm.id', '=', 'rmld.responsible_master_id')
//                     ->where('rm.Authorization_status', 'Expired')
//                     ->groupBy('rmld.license_type_id', 'rmld.license_name_id');
//             })
//             ->get();

//         $this->info('Raw expired licenses count: ' . $expiredLicensesRaw->count());

//         $expiredLicenses = $expiredLicensesRaw->filter(function ($responsible) {
//             $licenseDetail = $responsible->licenseDetails->first();
//             if (!$licenseDetail) {
//                 return true;
//             }
//             $licenseTypeId = $licenseDetail->license_type_id ?? null;
//             $licenseNameId = $licenseDetail->license_name_id ?? null;
//             if (!$licenseTypeId || !$licenseNameId) {
                
//                 return true;
//             }
//             $hasActive = ResponsibleMaster::where('Authorization_status', 'Active')
//                 ->whereHas('licenseDetails', function ($query) use ($licenseTypeId, $licenseNameId) {
//                     $query->where('license_type_id', $licenseTypeId)
//                           ->where('license_name_id', $licenseNameId);
//                 })
//                 ->exists();
           
//             return !$hasActive;
//         })->map(function ($responsible) {
//             $licenseDetail = $responsible->licenseDetails->first();
//             $managerEmail = null;
//             $managerName = 'N/A';
//             if ($responsible->employee && $responsible->employee->emp_reporting) {
//                 $manager = CoreEmployee::where('employee_id', $responsible->employee->emp_reporting)->first(['emp_name', 'emp_email']);
//                 if ($manager) {
//                     $managerName = $manager->emp_name ?? 'N/A';
//                     $managerEmail = $manager->emp_email ?? null;
//                 } else {
                    
//                 }
//             } else {
                
//             }
//             return [
//                 'emp_name' => optional($responsible->employee)->emp_name ?? 'N/A',
//                 'emp_email' => optional($responsible->employee)->emp_email ?? null,
//                 'manager_name' => $managerName,
//                 'manager_email' => $managerEmail,
//                 'company_name' => optional($responsible->company)->company_name ?? 'N/A',
//                 'certificate_no' => $responsible->certificate_no ?? 'N/A',
//                 'license_type' => $licenseDetail ? ($licenseDetail->licenseType->license_type ?? 'N/A') : 'N/A',
//                 'license_name' => $licenseDetail ? ($licenseDetail->licenseName->license_name ?? 'N/A') : 'N/A',
//                 'valid_up_to' => $responsible->Valid_up_to ? Carbon::parse($responsible->Valid_up_to)->format('d-m-Y') : 'N/A',
//             ];
//         });

//         $this->info('Filtered expired licenses count: ' . $expiredLicenses->count());

//         if ($expiredLicenses->isEmpty()) {
//             $this->info('No expired licenses found after filtering.');
//             return 0;
//         }

//         $recipients = [];
//         foreach ($users as $user) {
//             if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
//                 $recipients[$user->email] = $user->name ?? 'User';
//             } else {
//                 $this->warn("Invalid email for user: {$user->name} ({$user->email})");
//             }
//         }
//         foreach ($expiredLicenses as $license) {
//             if ($license['emp_email'] && filter_var($license['emp_email'], FILTER_VALIDATE_EMAIL)) {
//                 $recipients[$license['emp_email']] = $license['emp_name'];
//             } else {
//                 $this->warn("Invalid or missing employee email: {$license['emp_name']} ({$license['emp_email']})");
//             }
//             if ($license['manager_email'] && filter_var($license['manager_email'], FILTER_VALIDATE_EMAIL)) {
//                 $recipients[$license['manager_email']] = $license['manager_name'];
//             } else {
//                 $this->warn("Invalid or missing manager email: {$license['manager_name']} ({$license['manager_email']})");
//             }
//         }

//         $this->info('Recipients: ' . json_encode($recipients));

//         if (empty($recipients)) {
//             $this->info('No valid email recipients found.');
//             return 0;
//         }

//         $sentCount = 0;
//         $failedCount = 0;
      
//         foreach ($recipients as $email => $name) {
//             try {
//                 Mail::to($email)->send(new \App\Mail\ExpiredLicenseNotification($expiredLicenses->toArray()));
//                 $this->info("Email sent to: {$email} ({$name})");
//                 $sentCount++;
//                 // sleep(5); 
//             } catch (\Exception $e) {
//                 $this->error("Failed to send email to: {$email} ($name) - Error: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
                
//                 $failedCount++;
//             }
//         }

//         $this->info("Expired license emails processing completed. Sent: {$sentCount}, Failed: {$failedCount}");
//         return $failedCount > 0 ? 1 : 0;
//     }
// }


namespace App\Console\Commands;
use App\Models\ResponsibleMaster;
use App\Models\User;
use App\Models\CoreEmployee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendExpiredLicenseEmail extends Command
{
    protected $signature = 'email:expired-licenses';
    protected $description = 'Send email notifications about expired licenses to authorized users, employees, and their managers';

    public function handle()
    {
        $permission = \Spatie\Permission\Models\Permission::where('name', 'view-company Responsible Person')->first();
        if (!$permission) {
            $this->error('Permission "view-company Responsible Person" not found.');
            Log::error('Permission not found', ['name' => 'view-company Responsible Person']);
            return 1;
        }
        $this->info('Permission found: ' . $permission->name);

        $users = User::whereHas('permissions', function ($query) {
            $query->where('name', 'view-company Responsible Person');
        })->orWhereHas('roles.permissions', function ($query) {
            $query->where('name', 'view-company Responsible Person');
        })->get(['id', 'name', 'email']);

        if ($users->isEmpty()) {
            $this->info('No users found with view-company Responsible Person permission.');
        } else {
            $this->info('Found ' . $users->count() . ' users with permission:');
            foreach ($users as $user) {
                $this->info("User: {$user->name} ({$user->email})");
            }
        }

        $expiredLicensesRaw = ResponsibleMaster::with([
            'employee' => function ($query) {
                $query->select('id', 'emp_name', 'emp_email', 'emp_reporting');
            },
            'company' => function ($query) {
                $query->select('id', 'company_name');
            },
            'licenseDetails.licenseType' => function ($query) {
                $query->select('id', 'license_type');
            },
            'licenseDetails.licenseName' => function ($query) {
                $query->select('id', 'license_name');
            }
        ])
            ->where('Authorization_status', 'Expired')
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(rm.id)'))
                    ->from('responsible_masters as rm')
                    ->join('responsible_masters_license_details as rmld', 'rm.id', '=', 'rmld.responsible_master_id')
                    ->where('rm.Authorization_status', 'Expired')
                    ->groupBy('rmld.license_type_id', 'rmld.license_name_id');
            })
            ->get();

        $this->info('Raw expired licenses count: ' . $expiredLicensesRaw->count());

        $expiredLicenses = $expiredLicensesRaw->filter(function ($responsible) {
            $licenseDetail = $responsible->licenseDetails->first();
            if (!$licenseDetail) {
                return true;
            }
            $licenseTypeId = $licenseDetail->license_type_id ?? null;
            $licenseNameId = $licenseDetail->license_name_id ?? null;
            if (!$licenseTypeId || !$licenseNameId) {
                return true;
            }
            $hasActive = ResponsibleMaster::where('Authorization_status', 'Active')
                ->whereHas('licenseDetails', function ($query) use ($licenseTypeId, $licenseNameId) {
                    $query->where('license_type_id', $licenseTypeId)
                          ->where('license_name_id', $licenseNameId);
                })
                ->exists();
           
            return !$hasActive;
        })->map(function ($responsible) {
            $licenseDetail = $responsible->licenseDetails->first();
            $managerEmail = null;
            $managerName = 'N/A';
            $currentEmpId = $responsible->employee->emp_reporting;

            // Traverse reporting hierarchy
            while ($currentEmpId) {
                $manager = CoreEmployee::where('employee_id', $currentEmpId)->first(['emp_name', 'emp_email', 'emp_reporting']);
                if ($manager) {
                    $managerName = $manager->emp_name ?? 'N/A';
                    $managerEmail = $manager->emp_email ?? null;
                    $currentEmpId = $manager->emp_reporting;
                } else {
                    break;
                }
            }

            // If no manager found or top level (emp_reporting = 0), set to N/A
            if (!$managerEmail) {
                $managerName = 'N/A';
                $managerEmail = null;
            }

            return [
                'emp_name' => optional($responsible->employee)->emp_name ?? 'N/A',
                'emp_email' => optional($responsible->employee)->emp_email ?? null,
                'manager_name' => $managerName,
                'manager_email' => $managerEmail,
                'company_name' => optional($responsible->company)->company_name ?? 'N/A',
                'certificate_no' => $responsible->certificate_no ?? 'N/A',
                'license_type' => $licenseDetail ? ($licenseDetail->licenseType->license_type ?? 'N/A') : 'N/A',
                'license_name' => $licenseDetail ? ($licenseDetail->licenseName->license_name ?? 'N/A') : 'N/A',
                'valid_up_to' => $responsible->Valid_up_to ? Carbon::parse($responsible->Valid_up_to)->format('d-m-Y') : 'N/A',
            ];
        });

        $this->info('Filtered expired licenses count: ' . $expiredLicenses->count());

        if ($expiredLicenses->isEmpty()) {
            $this->info('No expired licenses found after filtering.');
            return 0;
        }

        $recipients = [];
        $ccRecipients = [];
        foreach ($users as $user) {
            if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                $recipients[$user->email] = $user->name ?? 'User';
            } else {
                $this->warn("Invalid email for user: {$user->name} ({$user->email})");
            }
        }
        foreach ($expiredLicenses as $license) {
            if ($license['emp_email'] && filter_var($license['emp_email'], FILTER_VALIDATE_EMAIL)) {
                $recipients[$license['emp_email']] = $license['emp_name'];
            } else {
                $this->warn("Invalid or missing employee email: {$license['emp_name']} ({$license['emp_email']})");
            }
            if ($license['manager_email'] && filter_var($license['manager_email'], FILTER_VALIDATE_EMAIL)) {
                $recipients[$license['manager_email']] = $license['manager_name'];
            } else if ($responsible->employee->emp_reporting == 0) {
                // Add to CC if top-level manager (emp_reporting = 0)
                $ccRecipients[$license['emp_email']] = $license['emp_name'];
            } else {
                $this->warn("Invalid or missing manager email: {$license['manager_name']} ({$license['manager_email']})");
            }
        }

        $this->info('Recipients: ' . json_encode($recipients));
        $this->info('CC Recipients: ' . json_encode($ccRecipients));

        if (empty($recipients)) {
            $this->info('No valid email recipients found.');
            return 0;
        }

        $sentCount = 0;
        $failedCount = 0;
      
        foreach ($recipients as $email => $name) {
            try {
                Mail::to($email)
                    ->cc(array_keys($ccRecipients))
                    ->send(new \App\Mail\ExpiredLicenseNotification($expiredLicenses->toArray()));
                $this->info("Email sent to: {$email} ({$name})");
                $sentCount++;
                // sleep(5); 
            } catch (\Exception $e) {
                $this->error("Failed to send email to: {$email} ($name) - Error: " . $e->getMessage() . " (Code: " . $e->getCode() . ")");
                $failedCount++;
            }
        }

        $this->info("Expired license emails processing completed. Sent: {$sentCount}, Failed: {$failedCount}");
        return $failedCount > 0 ? 1 : 0;
    }
}