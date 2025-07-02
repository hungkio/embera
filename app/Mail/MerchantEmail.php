<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MerchantEmail extends Mailable
{
    use Queueable, SerializesModels;

    public array $content;

    public function __construct(array $content)
    {
        $this->content = $content;
    }

    public function build()
    {
        return $this->subject('Hợp đồng từ hệ thống')
            ->view('admin.emails.merchant_revenue')
            ->with(['content' => $this->content]); // Blade sẽ tự fill
    }
}
