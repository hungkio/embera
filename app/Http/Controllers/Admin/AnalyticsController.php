<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Analytics;
use Spatie\Analytics\Period;

class AnalyticsController extends Controller
{
    public function __invoke()
    {
        $startDate = Carbon::today(config('app.timezone'))->startOfMonth();
        $endDate = Carbon::today(config('app.timezone'))->endOfMonth();
        $dimensions = 'hour';

        $period = Period::create($startDate, $endDate);

        $visitorData = [];

        $answer = Analytics::performQuery($period, 'ga:visits,ga:pageviews', ['dimensions' => 'ga:'.$dimensions]);

        if ($answer->rows == null) {
            $answer->rows = [];
        }

        if ($dimensions === 'hour') {
            foreach ($answer->rows as $dateRow) {
                $visitorData[] = [
                    'axis'      => (int) $dateRow[0].'h',
                    'visitors'  => $dateRow[1],
                    'pageViews' => $dateRow[2],
                ];
            }
        } else {
            foreach ($answer->rows as $dateRow) {
                $visitorData[] = [
                    'axis'      => Carbon::parse($dateRow[0])->toDateString(),
                    'visitors'  => $dateRow[1],
                    'pageViews' => $dateRow[2],
                ];
            }
        }

        $stats = collect($visitorData);

        $countryStats = Analytics::performQuery($period, 'ga:sessions',
            ['dimensions' => 'ga:countryIsoCode'])->rows;

        $total = Analytics::performQuery($period,
            'ga:sessions, ga:users, ga:pageviews, ga:percentNewSessions, ga:bounceRate, ga:pageviewsPerVisit, ga:avgSessionDuration, ga:newUsers')->totalsForAllResults;

        return view('admin.dashboards.analytics', compact('stats', 'total', 'countryStats'))->render();
    }
}
