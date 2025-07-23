<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MerchantEmail extends Mailable
{
    use Queueable, SerializesModels;

    public array $content;
    public string $shop_share_rate_type;

    public function __construct(array $content, string $shop_share_rate_type = 'percentage')
    {
        $this->content = $content;
        $this->shop_share_rate_type = $shop_share_rate_type;
    }

    public function build()
    {
        $template = $this->shop_share_rate_type === 'fixed'
            ? 'admin.emails.merchant_revenue_fixed'
            : 'admin.emails.merchant_revenue';

        return $this->subject('Hợp đồng từ hệ thống')
            ->view($template)
            ->with(['content' => $this->content]);
    }

}
