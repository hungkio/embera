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
