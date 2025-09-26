<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan email:test
     */
    protected $signature = 'email:test {email=yogitasahu1725@gmail.com}';

    /**
     * The console command description.
     */
    protected $description = 'Send a test email to verify SMTP settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $toEmail = $this->argument('email');

        try {
            Mail::raw('This is a test email from License Tracker.', function ($message) use ($toEmail) {
                $message->to($toEmail)
                        ->subject('Test Email from License Tracker');
            });

            $this->info("✅ Test email sent successfully to: {$toEmail}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test email: " . $e->getMessage());
        }

        return 0;
    }
}


//this is for testing the email functionality