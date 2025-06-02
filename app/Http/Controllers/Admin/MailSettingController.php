<?php

namespace App\Http\Controllers\Admin;

use App\Domain\MailSetting\Models\MailSetting;
use App\Domain\SubscribeEmail\Models\SubscribeEmail;
use App\Http\Requests\SaveMailRequest;
use App\Jobs\FormMailCustomer;
use App\Models\SubscribeGroup;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Notifications\FormMail;
use Illuminate\Support\Facades\Artisan;

class MailSettingController
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('view', MailSetting::class);
        $tabs = DB::table('mail_settings')->select('slug', 'name')->get();
        if ($tabs->isEmpty()) {
            $tabs = config('mail-settings.tabs', []);
        } else {
            $tabs = $tabs->keyBy('slug')->toArray();
            $arr_tab = [];
            foreach($tabs as $key => $tab)
            {
                $arr_tab[$key] = $tab->name;
            }
            $tabs = array_merge(config('mail-settings.tabs'), $arr_tab);
        }

        $default_tab = $request->get('tab', 'mail-template');
        $groups = config('mail-settings.mail-template', []);
        $default_values = [];
        if ($default_tab && $groups) {
            $default_values = self::getSettings($default_tab, false);
        }

        return view('admin.mail-settings.index', compact('tabs', 'groups', 'default_tab', 'default_values'));
    }

    function save(SaveMailRequest $request)
    {
        $this->authorize('create', MailSetting::class);
        $output = [];
        try {
            $tab = $request->get('tab', '');
            $name = $request->name;
            if ($tab == 'mail-template') {
                $name = config('mail-settings.tabs.' . $tab);
            }
            if (!$tab) {
                throw new \Exception('Dữ liệu sai !');
            }
            $groups = config('mail-settings.mail-template') ?: [];
            if ($groups) {
                $field_names = $this->getFieldName($groups);
                $values = [];
                foreach ($field_names as $field_name) {
                    $field_value = $request->get($field_name, null);
                    $values[$field_name] = $field_value;
                }

                $data = ['value' => \json_encode($values)];
                if ($name) {
                    $data = array_merge($data, [
                        'name'=> $name
                    ]);
                }

                $mail_setting = MailSetting::where('slug', $tab)->first();
                if ($mail_setting) {
                    $mail_setting->update($data);
                    logActivity($mail_setting, 'update');
                } else {
                    $mail_setting = MailSetting::create(array_merge([
                        'slug' => $tab
                    ], $data));
                    logActivity($mail_setting, 'create');
                }

                if ($request->mail_key) {
                    $request['slug'] = $tab;
                    $request['default_group'] = $values['default_group'];
                    return $this->send_mail_now($request);
                }
                $output['message'] = 'Lưu thành công !';
            } else {
                throw new \Exception('Error ' . $tab);
            }
        } catch (\Exception $exception) {
            $output['error'] = 1;
            $output['message'] = $exception->getMessage();
        }
        return response()->json($output);
    }

    static function getSettings($tab, $cache = true)
    {
        $cache_key = "mail_settings_{$tab}";
        if (!$cache) {
            Cache::forget($cache_key);
        }
        $option = Cache::remember($cache_key, now()->addDay(), function () use ($tab) {
            return \DB::table('mail_settings')
                ->where([
                    ['slug', $tab],
                ])
                ->first();
        });

        if (!empty($option->value)) {
            return !is_array($option->value) ? json_decode($option->value, true) : $option->value;
        }
        return [];
    }

    function getFieldName($groups)
    {
        $field_names = [];
        foreach ($groups as $key => $fields) {
            foreach ($fields as $field_id => $field_type) {
                $field_name = "{$key}_{$field_id}";
                $field_names[] = $field_name;
            }
        }
        return $field_names;
    }

    function send_mail_now(Request $request){
        $this->authorize('send', MailSetting::class);
        $mail_key = $request->get('mail_key');
        $slug = $request->slug ?? 'mail-template';
        $mail_template = site_get_mail_template($slug);
        //
        if ($mail_template) {
            $all_user = $mail_template["{$mail_key}_all"];
            $users = $mail_template["{$mail_key}_user"];
            if ($users) {
                $users = \array_map(function($user_id){
                    return SubscribeEmail::find($user_id);
                }, $users);
            }

            if($all_user == 1){
                $users = SubscribeEmail::all();
            } else if ($all_user == 2) { // group
                $group = SubscribeGroup::find($request->default_group);
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
                    self::send_mail_customer($mail_key, $mail_data, $mail_to, $slug);
                }
            }

            //
            //Artisan::call('queue:work');
            //
            return response()->json([
                'status' => 'success',
                'message' => __('Gửi mail thành công !'),
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => __('Bạn vui lòng lưu mẫu email trước khi gửi!'),
        ]);
    }

    function send_mail_customer($type, $mail_data, $mail_to, $slug)
    {
        $mail_template = site_get_mail_template($slug);
        $subject = $mail_template["{$type}_subject"];
        $body = $mail_template["{$type}_body"];
        if (empty($subject) || empty($body)) {
            return false;
        }
        $replace = [];
        foreach ($mail_data as $key => $value) {
            if (\is_array($value)) {
                continue;
            }
            $replace["[{$key}]"] = $value;
        }

        $subject = \str_replace(\array_keys($replace), \array_values($replace), $subject);
        $body = \str_replace(\array_keys($replace), \array_values($replace), $body);

        $body = view('shop.mail.mail-common', compact('body'))->render();
        //
        $mail_args = [
            'subject' => $subject,
            'body' => $body,
            'customer' => $mail_to,
        ];

        //gui mail
        dispatch(new FormMailCustomer($mail_args))->delay(\now()->addSeconds(30));
        return true;
    }

    public function delete($slug) {
        try {
            $mail_setting = MailSetting::where('slug', $slug)->firstOrFail();
            $this->authorize('delete', $mail_setting);

            logActivity($mail_setting, 'delete');
            $mail_setting->delete();
            return response()->json([
                'status' => 'success',
                'message' => __('Xoá chiến dịch thành công!'),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => __('Có lỗi xảy ra!'),
            ]);
        }
    }
}
