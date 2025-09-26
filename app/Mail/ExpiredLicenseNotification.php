<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpiredLicenseNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $expiredLicenses;

    public function __construct(array $expiredLicenses)
    {
        $this->expiredLicenses = $expiredLicenses;
    }

    public function build()
    {
        return $this->subject('Expired License Notification')
                    ->view('emails.expired_license')
                    ->with(['expiredLicenses' => $this->expiredLicenses]);
    }
}