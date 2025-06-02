<?php

namespace App\Jobs;

use App\Notifications\FormMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class FormMailAdmin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $mail_args;

    public function __construct($mail_args)
    {
        $this->mail_args = $mail_args;
    }

    public function handle()
    {
        //MAIL TO ADMIN
        $mail_to = setting('store_email') ?? env('MAIL_ADMIN');
        Notification::route('mail', $mail_to)->notify(new FormMail($this->mail_args));
    }
}
