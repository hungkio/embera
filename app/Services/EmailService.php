<?php

namespace App\Services;

use App\Models\Email;
use App\Mail\MerchantEmail;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function send(Email $email): bool
    {
        try {
            $content = $email->content?->text ?? '';

            Mail::to($email->to)->send(new MerchantEmail($content));

            $email->update(['status' => 'sent']);
            return true;
        } catch (\Throwable $e) {
            $email->update(['status' => 'failed']);
            report($e);
            return false;
        }
    }
}
