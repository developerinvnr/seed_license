<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResponsiblePersonLicenseDateExpiryReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        return $this->subject('Responsible Person License Date Expiry Reminder')
                    ->view('emails.responsible_person_expiry_reminder')
                    ->with('details', $this->details);
    }
}
