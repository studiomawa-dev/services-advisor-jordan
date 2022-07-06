<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Welcome to Services Advisor!';
        $address = $this->data['email'];
        $name = $this->data['name'];
        
        return $this->view('emails.welcome')
                    ->from($address, $name)
                    ->subject($subject)
                    ->with([ 'data' => $this->data]);
    }
}
