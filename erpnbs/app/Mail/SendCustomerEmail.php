<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCustomerEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $lArr;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $lArr)
    {

        $this->lArr  = $lArr;
        $this->subject = $lArr['subject'] ?? 'Default Subject'; // Use a default if 'subject' is missing


    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('email.customer_mail')->with('lArr', $this->lArr)->subject($this->subject);

    }
}
