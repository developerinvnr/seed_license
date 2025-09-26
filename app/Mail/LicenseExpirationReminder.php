<?php

namespace App\Mail;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LicenseExpirationReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $license;

    /**
     * Create a new message instance.
     *
     * @param License $license
     * @return void
     */
    public function __construct(License $license)
    {
        $this->license = $license;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.license_expiration_reminder')
            ->with([
                'license' => $this->license,
            ])
            ->subject('License Expiration Reminder');
    }
}
