<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyRevenueReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $path;
    public $date;

    public function __construct($path, Carbon $date)
    {
        $this->path = $path;
        $this->date = $date;
    }

    public function build()
    {
        return $this->subject('Báo cáo doanh thu ngày ' . $this->date->format('d/m/Y'))
                    ->view('admin.emails.daily_revenue')
                    ->attach($this->path);
    }
}
