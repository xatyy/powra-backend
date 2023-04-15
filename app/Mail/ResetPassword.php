<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;
  
    public $url_reset = false;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url_reset)
    {
        //
      $this->url_reset = $url_reset;
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
        return $this->markdown('emails.reset_password')->subject('Reset password');
    }
}