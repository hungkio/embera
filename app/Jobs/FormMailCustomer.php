<?php

namespace App\Jobs;

use App\Notifications\FormMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class FormMailCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $mail_args;

    public function __construct($mail_args)
    {
        $this->mail_args = $mail_args;
    }

    public function handle()
    {
        Notification::route('mail', $this->mail_args['customer'])->notify(new FormMail($this->mail_args));
    }
}
