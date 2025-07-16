<?php

use App\Support\ValuesStore\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

if (!function_exists('array_reset_index')) {
    /**
     * Reset numeric index of an array recursively.
     *
     * @param array $array
     * @return array|\Illuminate\Support\Collection
     *
     * @see https://stackoverflow.com/a/12399408/5736257
     */
    function array_reset_index($array): array
    {
        $array = $array instanceof Collection
            ? $array->toArray()
            : $array;

        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $array[$key] = array_reset_index($val);
            }
        }

        if (isset($key) && is_numeric($key)) {
            return array_values($array);
        }

        return $array;
    }
}
if (!function_exists('setting')) {
    function setting($key = null, $default = null)
    {
        if ($key === null) {
            return app(Setting::class);
        }

        return app(Setting::class)->get($key, $default);
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date): string
    {
        if (!$date instanceof Carbon) {
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        }

        return $date->format(setting('date_format', 'Y-m-d H:i:s'));
    }
}

if (!function_exists('intended')) {
    function intended($request, string $defaultUrl)
    {
        if (!empty($request->redirect_url)) {
            return redirect($request->redirect_url);
        }

        return redirect()->to($defaultUrl);
    }
}

function formatNumber($value)
{
    return number_format($value);
}

if (!function_exists('currentUser')) {
    function currentUser()
    {
        return Auth::guard('web')->user();
    }
}

if (!function_exists('currentAdmin')) {
    function currentAdmin()
    {
        return Auth::guard('admins')->user();
    }
}

if (!function_exists('logActivity')) {
    function logActivity($subjectModel, $actionName, $customProperties = [])
    {
//        $activity = activity();
//        $activity->causedBy(auth()->user());
//        if ($subjectModel) {
//            $activity->performedOn($subjectModel);
//        }
//        if (!empty($customProperties)) {
//            $activity->withProperties($customProperties);
//        }
//        $activity->log($actionName);
//        return $activity;
    }
}
if (!function_exists('site_get_mail_template')) {
    function site_get_mail_template($slug)
    {
        $option = \DB::table('mail_settings')
            ->where([
                ['slug', $slug],
            ])
            ->first();

        if (!empty($option->value)) {
            return !is_array($option->value) ? \json_decode($option->value, true) : $option->value;
        }
        return [];
    }
}

if (!function_exists('display_country_name')) {
    function display_country_name($code)
    {
        $countrie = \DB::table('countries')
            ->where([
                ['code', $code],
            ])
            ->first();

        if (!empty($countrie->name)) {
            return $countrie->name;
        }
        return $code;
    }
}

if (!function_exists('sendZaloZNS')) {
    // để lấy refresh token, truy cập: https://oauth.zaloapp.com/v4/oa/permission?app_id=2138008220222428783&redirect_uri=https%3A%2F%2Fembera.tech%2Fadmin%2Fshops để lấy code, sau đó gọi function getZaloAccessToken($code = null) để lưu refresh_token vào cache
    function sendZaloZNS($phone, $templateId, array $params)
    {
        $res = getZaloAccessToken();

        // chia sẻ theo số lượng giao dịch
//        $send = sendZaloZNS("84345281681", 466895, [
//            'number_of_order' => 100,
//            'thang_giao_dich' => "07/2025",
//            'ma_hop_dong' => "03/2025/HĐNT-EBR",
//            'customer_name' => "Zoom Coffee (MB-HN-CG)",
//            'share_money' => 300000,
//            'share_percent' => "3000",
//        ]);

        // chia sẻ theo % doanh thu
//        $send = sendZaloZNS("84345281681", 466893, [
//            'total' => 300000,
//            'thang_giao_dich' => "07/2025",
//            'ma_hop_dong' => "03/2025/HĐNT-EBR",
//            'customer_name' => "Zoom Coffee (MB-HN-CG)",
//            'share_money' => 30000,
//            'share_percent' => "10",
//        ]);

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type'  => 'application/json',
            'access_token'  => $res['access_token'],
        ])->post('https://business.openapi.zalo.me/message/template', [
            'phone'         => $phone,
            'template_id'   => $templateId,
            'tracking_id'   => uniqid('zns_'),
            'template_data' => $params,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        \Log::error('Zalo ZNS gửi thất bại', [
            'status'   => $response->status(),
            'response' => $response->body(),
        ]);

        return false;
    }
}

if (!function_exists('getZaloAccessToken')) {
    function getZaloAccessToken($code = null) {
        if ($code) {
            $payload = [
                'code' => $code,
                'app_id' => env('OA_APP_ID'),
                'grant_type' => 'authorization_code',
                'code_verifier' => 'your_code_verifier',
            ];
        } else {
            $refreshToken = \Illuminate\Support\Facades\Cache::get('OA_APP_REFRESH_TOKEN');
            $payload = [
                'refresh_token' => $refreshToken,
                'app_id' => env('OA_APP_ID'),
                'grant_type' => 'refresh_token',
            ];
        }

        $response = Http::asForm()
            ->withHeaders(['secret_key' => env('OA_APP_SECRET')])
            ->post('https://oauth.zaloapp.com/v4/oa/access_token', $payload);

        if ($response->json()) {
            \Illuminate\Support\Facades\Cache::put('OA_APP_REFRESH_TOKEN', $response->json()['refresh_token'], now()->addDays(60));
        }
        return $response->json();
    }
}

