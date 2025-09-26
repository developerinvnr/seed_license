<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\License;
use App\Mail\LicenseExpirationReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SendLicenseReminders extends Command
{
    protected $signature = 'licenses:send-reminders';
    protected $description = 'Send reminder emails for licenses expiring soon';

    public function handle()
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

        $this->info('Reminder emails sent successfully.');
    }
}
