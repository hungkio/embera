<?php

namespace App\Jobs;

use App\Models\ToolRedirecter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PutRedirectFileToFrontend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {

    }

    public function handle()
    {
        try {
            $data = ToolRedirecter::select(['origin_link', 'redirect_link', 'start_at', 'end_at'])
                ->where('status', 1) // Active
                ->get()
                ->toArray();

            Storage::disk('frontend')->put('redirect.json', json_encode($data));
        } catch (\Throwable $th) {
            \Log::error($th);
        }
    }
}
