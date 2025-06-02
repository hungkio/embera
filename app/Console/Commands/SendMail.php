<?php

namespace App\Console\Commands;

use App\Domain\MailSetting\Models\MailSetting;
use App\Domain\SubscribeEmail\Models\SubscribeEmail;
use App\Http\Controllers\Admin\MailSettingController;
use App\Models\SubscribeGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = MailSetting::all();
        $mailSettingController = new MailSettingController();
        foreach ($settings as $setting) {
            $slug = $setting->slug;
            $config = json_decode($setting->value, 1);
            $schedule = $config['default_schedule'];
            $time = Carbon::parse($schedule)->format('Y-m-d H:i');
            if ($time != Carbon::now()->format('Y-m-d H:i')) {
                continue;
            }

            $all_user = $config['default_all'];
            $users = $config['default_user'];
            if ($users) {
                $users = \array_map(function($user_id){
                    return SubscribeEmail::find($user_id);
                }, $users);
            }

            if($all_user == 1){
                $users = SubscribeEmail::all();
            } else if ($all_user == 2) { // group
                $group = SubscribeGroup::find($config['default_group']);
                $email_ids = $group->email_ids ? json_decode($group->email_ids) : [];
                $users = SubscribeEmail::find($email_ids);
            }

            if(empty($users) || !$users){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa chọn người nhận !',
                ]);
            }

            //
            foreach($users as $user){
                $mail_to = $user->email;
                if($mail_to){
                    $mail_data = [
                        'name' => $user->email,
                    ];
                    $mailSettingController->send_mail_customer('default', $mail_data, $mail_to, $slug);
                }
            }
        }

        //
    }
}
