<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Powra extends Mailable
{
    use Queueable, SerializesModels;
  
    public $powra_report = false;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($powra_report)
    {
        //
      $this->powra_report = $powra_report;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->withSwiftMessage(function ($message) {
        $message->getHeaders()
                ->addTextHeader('Custom-Header', 'HeaderValue');
        });
        return $this->markdown('emails.powra')->subject('New report');
    }
}