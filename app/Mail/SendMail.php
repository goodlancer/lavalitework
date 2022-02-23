<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    public $billData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($billData)
    {
        $this->billData=$billData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('testrevin@gmail.com','revin')
                    ->subject('Payment Bill')
                    ->view('mailBill')->with(['rows'=>$this->billData]);
    }
}
