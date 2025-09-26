<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;

    public function __construct($name = "User")
    {
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Welcome to License Tracking System')
                    ->view('emails.welcome');
    }
}
