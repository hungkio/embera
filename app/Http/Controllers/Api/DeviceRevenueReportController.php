<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceStatus;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DeviceRevenueReportController extends Controller
{
    private const GOOD_REVENUE = 300000;
    private const WATCH_REVENUE = 100000;
    private const KPI_REVENUE = 200000;
    private const REPORT_PAYMENT_CHANNELS = ['balance', 'mbpay'];
    private const EXCLUDED_ORDER_AMOUNTS = [240000, 364000];

    public function index(Request $request): JsonResponse
    {
        $reportYear = (int) $request->input('year', now()->year);
        $months = $this->reportMonths($request, $reportYear);
        $currentFrom = now()->startOfMonth();
        $currentTo = now()->endOfDay();

        $devices = DeviceStatus::query()
            ->with('shop')
            ->get()
            ->keyBy('code');

        $periodRevenue = $this->getMonthlyRevenueFromOrders($months, $request);
        $currentRevenue = $this->getRevenueBetween($currentFrom, $currentTo, $request);
        $latestOrders = $this->getLatestOrders($request);

        $deviceCodes = $periodRevenue->keys()
            ->merge($currentRevenue->keys())
            ->merge($latestOrders->keys())
            ->filter()
            ->unique()
            ->values();

        $rows = $deviceCodes->map(function (string $deviceCode, int $index) use (
            $devices,
            $periodRevenue,
            $currentRevenue,
            $latestOrders
        ) {
            $device = $devices->get($deviceCode);
            $latestOrder = $latestOrders->get($deviceCode);
            $revenue = $periodRevenue->get($deviceCode, [
                'ngay_lap' => null,
                'doanh_thu_t1' => 0,
                'doanh_thu_t2' => 0,
                'doanh_thu_t3' => 0,
            ]);
            $installDate = $this->getInstallDate($device, $latestOrder);

            $revenueT1 = (float) $revenue['doanh_thu_t1'];
            $revenueT2 = (float) $revenue['doanh_thu_t2'];
            $revenueT3 = (float) $revenue['doanh_thu_t3'];
            $current = (float) ($currentRevenue->get($deviceCode) ?? 0);
            $currentEvaluation = $this->evaluateRevenue($current);
            $isOnline = ($device?->status ?? 'offline') === 'online';

            return [
                'stt' => $index + 1,
                'cum' => $latestOrder?->region,
                'khu_vuc' => $latestOrder?->city ?? $latestOrder?->area,
                'nhan_vien_pt' => $latestOrder?->employee_name,
                'ma_may' => $deviceCode,
                'ten_diem' => $device?->shop?->name ?? $latestOrder?->rental_shop,
                'loai_diem' => $latestOrder?->rental_shop_type,
                'ngay_lap' => $installDate?->format('Y-m-d'),
                'doanh_thu_t1' => $revenueT1,
                'doanh_thu_t2' => $revenueT2,
                'doanh_thu_t3' => $revenueT3,
                'danh_gia_t1' => $this->evaluateRevenue($revenueT1),
                'danh_gia_t2' => $this->evaluateRevenue($revenueT2),
                'danh_gia_t3' => $this->evaluateRevenue($revenueT3),
                'doanh_thu_hien_tai' => $current,
                'trang_thai_hien_tai' => $isOnline ? 'online' : 'offline',
                'dat_kpi_dt_200k' => $current >= self::KPI_REVENUE,
                'ngay_xac_dinh_trang_thai' => now()->toDateString(),
                'hanh_dong_bat_buoc' => $this->requiredAction($currentEvaluation, $current, $revenueT3, $isOnline),
                'deadline_xu_ly' => $this->deadline($currentEvaluation, $current, $revenueT3),
                'canh_bao' => $this->warning($currentEvaluation, $isOnline),
                'bien_dong_t2_t1' => $this->revenueRatio($revenueT2, $revenueT1),
                'bien_dong_t3_t1' => $this->revenueRatio($revenueT3, $revenueT1),
            ];
        });

        return response()->json([
            'columns' => $this->columns(),
            'meta' => [
                'current_from' => $currentFrom->toDateString(),
                'current_to' => $currentTo->toDateString(),
                'year' => $reportYear,
                'months' => [
                    't1' => $months[0]->format('Y-m'),
                    't2' => $months[1]->format('Y-m'),
                    't3' => $months[2]->format('Y-m'),
                ],
                'rules' => [
                    't1' => 'Doanh thu từ orders của tháng T1',
                    't2' => 'Doanh thu từ orders của tháng T2',
                    't3' => 'Doanh thu từ orders của tháng T3',
                    'danh_gia' => [
                        'Tốt' => '>= 300.000đ',
                        'Theo dõi' => '100.000đ đến dưới 300.000đ',
                        'Kém' => '< 100.000đ',
                    ],
                    'kpi_cum' => 'Cụm đạt KPI khi >=60% điểm có doanh thu hiện tại >=200.000đ và <=30% điểm kém',
                ],
                'kpi_cum' => $this->clusterKpis($rows),
                'total' => $rows->count(),
            ],
            'data' => $rows->values(),
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $deviceCodes = $this->parseDeviceCodes($request);

        if ($deviceCodes->isEmpty()) {
            return response()->json([
                'message' => 'Vui lòng truyền danh sách mã máy qua field device_codes.',
                'example' => [
                    'device_codes' => ['VNS011A00481', 'VNSBABP00134'],
                ],
            ], 422);
        }

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Order::query()
                ->whereIn('rental_equipment_id', $deviceCodes)
                ->whereNotNull('payment_time')
                ->min('payment_time');

        $startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfYear();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfDay();

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        $rows = Order::query()
            ->whereIn('rental_equipment_id', $deviceCodes)
            ->whereNotNull('payment_time')
            ->where('order_amount', '>', 0)
            ->whereNotIn('order_amount', self::EXCLUDED_ORDER_AMOUNTS)
            ->whereIn('payment_channels', self::REPORT_PAYMENT_CHANNELS)
            ->whereBetween('payment_time', [$startDate, $endDate])
            ->select('rental_equipment_id')
            ->selectRaw('DATE_FORMAT(payment_time, "%Y-%m") as month')
            ->selectRaw('SUM(order_amount) as revenue')
            ->selectRaw('COUNT(*) as order_count')
            ->groupBy('rental_equipment_id', 'month')
            ->orderBy('month')
            ->get();

        $months = collect();
        $cursor = $startDate->copy()->startOfMonth();
        $lastMonth = $endDate->copy()->startOfMonth();

        while ($cursor->lte($lastMonth)) {
            $months->push($cursor->format('Y-m'));
            $cursor->addMonthNoOverflow();
        }

        $rowsByDevice = $rows->groupBy('rental_equipment_id');
        $rowsByMonth = $rows->groupBy('month');

        $deviceTotals = $deviceCodes->map(function (string $deviceCode) use ($rowsByDevice) {
            $rows = $rowsByDevice->get($deviceCode, collect());

            return [
                'device_code' => $deviceCode,
                'total_revenue' => (float) $rows->sum('revenue'),
                'order_count' => (int) $rows->sum('order_count'),
            ];
        })->values();

        $monthlyTotals = $months->map(function (string $month) use ($rowsByMonth) {
            $rows = $rowsByMonth->get($month, collect());

            return [
                'month' => $month,
                'total_revenue' => (float) $rows->sum('revenue'),
                'order_count' => (int) $rows->sum('order_count'),
            ];
        })->values();

        $deviceMonthlyRevenue = $deviceCodes->map(function (string $deviceCode) use ($months, $rowsByDevice) {
            $rows = $rowsByDevice->get($deviceCode, collect())->keyBy('month');

            return [
                'device_code' => $deviceCode,
                'months' => $months->map(function (string $month) use ($rows) {
                    $row = $rows->get($month);

                    return [
                        'month' => $month,
                        'revenue' => (float) ($row->revenue ?? 0),
                        'order_count' => (int) ($row->order_count ?? 0),
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'meta' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'device_count' => $deviceCodes->count(),
                'rules' => [
                    'date_field' => 'payment_time',
                    'device_field' => 'rental_equipment_id',
                    'included_payment_channels' => self::REPORT_PAYMENT_CHANNELS,
                    'excluded_order_amounts' => self::EXCLUDED_ORDER_AMOUNTS,
                ],
            ],
            'device_totals' => $deviceTotals,
            'monthly_totals' => $monthlyTotals,
            'device_monthly_revenue' => $deviceMonthlyRevenue,
        ]);
    }

    public function weeklyRevenue(Request $request): JsonResponse
    {
        $reportYear = (int) $request->input('year', now()->year);
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::create($reportYear, 1, 1)->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfDay();

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        // Generate all weeks in the range
        $weeks = collect();
        $current = $startDate->copy()->startOfWeek(Carbon::MONDAY);
        $end = $endDate->copy();

        while ($current->lte($end)) {
            $weekKey = $current->isoWeekYear() . '-W' . str_pad((string) $current->isoWeek(), 2, '0', STR_PAD_LEFT);
            $weeks->put($weekKey, [
                'week' => $weekKey,
                'week_start' => $current->toDateString(),
                'week_end' => $current->copy()->endOfWeek(Carbon::SUNDAY)->toDateString(),
            ]);
            $current->addWeek();
        }

        $dbDriver = DB::connection()->getDriverName();
        if ($dbDriver === 'sqlite') {
            $weekStartRaw = "date(return_time, '-6 days', 'weekday 1')";
        } else {
            $weekStartRaw = "DATE(DATE_SUB(CONVERT_TZ(return_time, '+00:00', '+07:00'), INTERVAL WEEKDAY(CONVERT_TZ(return_time, '+00:00', '+07:00')) DAY))";
        }

        $query = Order::query()
            ->whereNotNull('rental_equipment_id')
            ->where('rental_equipment_id', '!=', '')
            ->whereNotNull('return_time')
            ->where('order_amount', '>', 0)
            ->whereNotIn('order_amount', self::EXCLUDED_ORDER_AMOUNTS)
            ->whereIn('payment_channels', self::REPORT_PAYMENT_CHANNELS)
            ->whereBetween('return_time', [$startDate, $endDate]);

        $this->applyOrderFilters($query, $request);

        $rows = $query
            ->select('rental_equipment_id')
            ->selectRaw($weekStartRaw . ' as week_start')
            ->selectRaw('SUM(order_amount) as total_revenue')
            ->selectRaw('COUNT(*) as order_count')
            ->groupBy('rental_equipment_id', 'week_start')
            ->havingRaw('SUM(order_amount) > 0')
            ->orderBy('week_start')
            ->orderBy('rental_equipment_id')
            ->get();

        $devices = DeviceStatus::query()
            ->with('shop')
            ->get()
            ->keyBy('code');

        $latestOrders = $this->getLatestOrders($request);

        $deviceCodes = $rows->pluck('rental_equipment_id')
            ->merge($latestOrders->keys())
            ->filter()
            ->unique()
            ->values();

        $data = $deviceCodes->map(function (string $deviceCode) use ($weeks, $rows, $devices, $latestOrders) {
            $device = $devices->get($deviceCode);
            $latestOrder = $latestOrders->get($deviceCode);
            $installDate = $this->getInstallDate($device, $latestOrder);
            $isOnline = ($device?->status ?? 'offline') === 'online';

            $deviceRows = $rows->where('rental_equipment_id', $deviceCode)->keyBy('week_start');

            $rowResult = [
                'device_code' => $deviceCode,
                'cum' => $latestOrder?->region,
                'khu_vuc' => $latestOrder?->city ?? $latestOrder?->area,
                'nhan_vien_pt' => $latestOrder?->employee_name,
                'ten_diem' => $device?->shop?->name ?? $latestOrder?->rental_shop,
                'loai_diem' => $latestOrder?->rental_shop_type,
                'ngay_lap' => $installDate?->format('Y-m-d'),
                'trang_thai_hien_tai' => $isOnline ? 'online' : 'offline',
            ];

            $totalRevenue = 0.0;
            $totalOrderCount = 0;
            $weeksList = [];

            foreach ($weeks as $weekKey => $weekInfo) {
                $row = $deviceRows->get($weekInfo['week_start']);
                $revenue = $row ? (float) $row->total_revenue : 0.0;
                $orderCount = $row ? (int) $row->order_count : 0;

                $rowResult[$weekKey] = $revenue;

                $totalRevenue += $revenue;
                $totalOrderCount += $orderCount;

                $weeksList[] = [
                    'week' => $weekKey,
                    'week_start' => $weekInfo['week_start'],
                    'week_end' => $weekInfo['week_end'],
                    'revenue' => $revenue,
                    'order_count' => $orderCount,
                ];
            }

            $rowResult['weeks'] = $weeksList;
            $rowResult['total_revenue'] = $totalRevenue;
            $rowResult['order_count'] = $totalOrderCount;

            return $rowResult;
        })
        ->sortByDesc('total_revenue')
        ->values();

        $weeklyTotals = $weeks->map(function ($weekInfo) use ($rows) {
            $weekRows = $rows->where('week_start', $weekInfo['week_start']);

            return [
                'week' => $weekInfo['week'],
                'week_start' => $weekInfo['week_start'],
                'week_end' => $weekInfo['week_end'],
                'total_revenue' => (float) $weekRows->sum('total_revenue'),
                'order_count' => (int) $weekRows->sum('order_count'),
            ];
        })->values();

        $deviceTotals = $data->map(function ($deviceItem) {
            return [
                'device_code' => $deviceItem['device_code'],
                'cum' => $deviceItem['cum'],
                'khu_vuc' => $deviceItem['khu_vuc'],
                'nhan_vien_pt' => $deviceItem['nhan_vien_pt'],
                'ten_diem' => $deviceItem['ten_diem'],
                'loai_diem' => $deviceItem['loai_diem'],
                'ngay_lap' => $deviceItem['ngay_lap'],
                'trang_thai_hien_tai' => $deviceItem['trang_thai_hien_tai'],
                'total_revenue' => $deviceItem['total_revenue'],
                'order_count' => $deviceItem['order_count'],
            ];
        })
        ->sortByDesc('total_revenue')
        ->values();

        return response()->json([
            'meta' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'group_by' => 'iso_week',
                'date_field' => 'return_time',
                'device_field' => 'rental_equipment_id',
                'included_payment_channels' => self::REPORT_PAYMENT_CHANNELS,
                'excluded_order_amounts' => self::EXCLUDED_ORDER_AMOUNTS,
                'weeks' => $weeks->values(),
                'total_rows' => $data->count(),
            ],
            'data' => $data,
            'weekly_totals' => $weeklyTotals,
            'device_totals' => $deviceTotals,
        ]);
    }

    private function parseDeviceCodes(Request $request): Collection
    {
        $codes = $request->input('device_codes', $request->input('devices', []));

        if (is_string($codes)) {
            $codes = preg_split('/[\s,;]+/', $codes);
        }

        return collect($codes)
            ->map(fn ($code) => trim((string) $code))
            ->filter()
            ->unique()
            ->values();
    }

    private function getMonthlyRevenueFromOrders(Collection $months, Request $request): Collection
    {
        $start = $months->first()->copy()->startOfMonth();
        $end = $months->last()->copy()->endOfMonth();

        $query = Order::query()
            ->whereNotNull('rental_equipment_id')
            ->whereNotNull('payment_time')
            ->where('order_amount', '>', 0)
            ->whereNotIn('order_amount', self::EXCLUDED_ORDER_AMOUNTS)
            ->whereIn('payment_channels', self::REPORT_PAYMENT_CHANNELS)
            ->whereBetween('payment_time', [$start, $end]);

        $this->applyOrderFilters($query, $request);

        return $query
            ->groupBy('rental_equipment_id')
            ->selectRaw('rental_equipment_id')
            ->selectRaw('SUM(CASE WHEN YEAR(payment_time) = ? AND MONTH(payment_time) = ? THEN order_amount ELSE 0 END) as doanh_thu_t1', [$months[0]->year, $months[0]->month])
            ->selectRaw('SUM(CASE WHEN YEAR(payment_time) = ? AND MONTH(payment_time) = ? THEN order_amount ELSE 0 END) as doanh_thu_t2', [$months[1]->year, $months[1]->month])
            ->selectRaw('SUM(CASE WHEN YEAR(payment_time) = ? AND MONTH(payment_time) = ? THEN order_amount ELSE 0 END) as doanh_thu_t3', [$months[2]->year, $months[2]->month])
            ->get()
            ->keyBy('rental_equipment_id')
            ->map(fn ($item) => [
                'doanh_thu_t1' => (float) $item->doanh_thu_t1,
                'doanh_thu_t2' => (float) $item->doanh_thu_t2,
                'doanh_thu_t3' => (float) $item->doanh_thu_t3,
            ]);
    }

    private function reportMonths(Request $request, int $reportYear): Collection
    {
        $dateRange = $request->input('date_range');

        if (is_string($dateRange) && str_contains($dateRange, ' - ')) {
            [$from, $to] = explode(' - ', $dateRange);
            $start = Carbon::parse($from)->startOfMonth();
            $end = Carbon::parse($to)->startOfMonth();
            $months = collect();

            while ($start <= $end && $months->count() < 3) {
                $months->push($start->copy());
                $start->addMonthNoOverflow();
            }

            while ($months->count() < 3) {
                $months->push($months->last()?->copy()->addMonthNoOverflow() ?? Carbon::create($reportYear, 1, 1));
            }

            return $months;
        }

        return collect([1, 2, 3])->map(fn (int $month) => Carbon::create($reportYear, $month, 1)->startOfMonth());
    }

    private function getRevenueBetween(Carbon $start, Carbon $end, Request $request): Collection
    {
        $query = Order::query()
            ->whereNotNull('rental_equipment_id')
            ->whereNotNull('payment_time')
            ->where('order_amount', '>', 0)
            ->whereNotIn('order_amount', self::EXCLUDED_ORDER_AMOUNTS)
            ->whereIn('payment_channels', self::REPORT_PAYMENT_CHANNELS)
            ->whereBetween('payment_time', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);

        $this->applyOrderFilters($query, $request);

        return $query
            ->groupBy('rental_equipment_id')
            ->select('rental_equipment_id', DB::raw('SUM(order_amount) as total_revenue'))
            ->pluck('total_revenue', 'rental_equipment_id');
    }

    private function getLatestOrders(Request $request): Collection
    {
        $query = Order::query()
            ->whereNotNull('rental_equipment_id')
            ->whereNotNull('payment_time')
            ->where('order_amount', '>', 0)
            ->whereNotIn('order_amount', self::EXCLUDED_ORDER_AMOUNTS)
            ->whereIn('payment_channels', self::REPORT_PAYMENT_CHANNELS);

        $this->applyOrderFilters($query, $request);

        return $query
            ->orderByDesc('payment_time')
            ->get()
            ->unique('rental_equipment_id')
            ->keyBy('rental_equipment_id');
    }

    private function applyOrderFilters($query, Request $request): void
    {
        if ($request->filled('staff')) {
            $query->where('employee_name', $request->input('staff'));
        }

        if ($request->filled('shop_type')) {
            $query->where('rental_shop_type', $request->input('shop_type'));
        }

        if ($request->filled('shop_name')) {
            $query->whereIn('rental_shop', array_filter((array) $request->input('shop_name')));
        }

        if ($request->filled('payment_channel')) {
            $query->where('payment_channels', $request->input('payment_channel'));
        }

        if ($request->filled('region')) {
            $query->where('region', $request->input('region'));
        }

        if ($request->filled('city')) {
            $query->where('city', $request->input('city'));
        }

        if ($request->filled('area')) {
            $query->where('area', $request->input('area'));
        }

        if ($request->filled('merchant_name')) {
            $query->whereIn('merchant_name', array_filter((array) $request->input('merchant_name')));
        }
    }

    private function getInstallDate($device, $latestOrder): ?Carbon
    {
        if ($device?->created_at) {
            return Carbon::parse($device->created_at);
        }

        if ($latestOrder?->rental_time) {
            return Carbon::parse($latestOrder->rental_time);
        }

        return null;
    }

    private function parseDate(?string $date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date);
        } catch (\Throwable) {
            return null;
        }
    }

    private function evaluateRevenue(float $revenue): string
    {
        if ($revenue >= self::GOOD_REVENUE) {
            return 'Tốt';
        }

        if ($revenue >= self::WATCH_REVENUE) {
            return 'Theo dõi';
        }

        return 'Kém';
    }

    private function requiredAction(string $evaluation, float $currentRevenue, float $revenueT3, bool $isOnline): ?string
    {
        if (!$isOnline) {
            return 'Kiểm tra thiết bị offline';
        }

        if ($evaluation === 'Kém') {
            return 'Lập phương án xử lý điểm kém trong 7 ngày';
        }

        if ($evaluation === 'Theo dõi' && $currentRevenue <= $revenueT3) {
            return 'Chuyển diện xử lý do chưa cải thiện sau theo dõi';
        }

        if ($evaluation === 'Theo dõi') {
            return 'Theo dõi thêm 30 ngày';
        }

        return null;
    }

    private function deadline(string $evaluation, float $currentRevenue, float $revenueT3): ?string
    {
        if ($evaluation === 'Kém' || ($evaluation === 'Theo dõi' && $currentRevenue <= $revenueT3)) {
            return now()->addDays(7)->toDateString();
        }

        if ($evaluation === 'Theo dõi') {
            return now()->addDays(30)->toDateString();
        }

        return null;
    }

    private function warning(string $evaluation, bool $isOnline): ?string
    {
        if (!$isOnline) {
            return 'Thiết bị đang offline';
        }

        if ($evaluation === 'Kém') {
            return 'Điểm kém, cần xử lý trong 7 ngày';
        }

        if ($evaluation === 'Theo dõi') {
            return 'Điểm cần theo dõi thêm';
        }

        return null;
    }

    private function revenueRatio(float $numerator, float $denominator): float
    {
        if ($denominator == 0.0) {
            return 0.0;
        }

        return round($numerator / $denominator, 2);
    }

    private function clusterKpis(Collection $rows): array
    {
        return $rows
            ->groupBy(fn (array $row) => $row['cum'] ?: 'Chưa xác định')
            ->map(function (Collection $items, string $cluster) {
                $total = $items->count();
                $kpiCount = $items->where('dat_kpi_dt_200k', true)->count();
                $badCount = $items->filter(fn (array $row) => $row['doanh_thu_hien_tai'] < self::WATCH_REVENUE)->count();
                $kpiRate = $total > 0 ? round(($kpiCount / $total) * 100, 2) : 0.0;
                $badRate = $total > 0 ? round(($badCount / $total) * 100, 2) : 0.0;

                return [
                    'cum' => $cluster,
                    'tong_diem' => $total,
                    'so_diem_dat_kpi_dt_200k' => $kpiCount,
                    'so_diem_kem' => $badCount,
                    'ty_le_dat_kpi' => $kpiRate,
                    'ty_le_diem_kem' => $badRate,
                    'dat_kpi_cum' => $kpiRate >= 60 && $badRate <= 30,
                ];
            })
            ->values()
            ->all();
    }

    private function columns(): array
    {
        return [
            'cum' => 'Cụm',
            'khu_vuc' => 'Khu vực',
            'nhan_vien_pt' => 'Nhân viên PT',
            'ma_may' => 'Mã máy',
            'ten_diem' => 'Tên điểm',
            'loai_diem' => 'Loại điểm',
            'ngay_lap' => 'Ngày lắp',
            'doanh_thu_t1' => 'Doanh thu T1',
            'doanh_thu_t2' => 'Doanh thu T2',
            'doanh_thu_t3' => 'Doanh thu T3',
            'danh_gia_t1' => 'Đánh giá T1',
            'danh_gia_t2' => 'Đánh giá T2',
            'danh_gia_t3' => 'Đánh giá T3',
            'doanh_thu_hien_tai' => 'Doanh thu hiện tại',
            'trang_thai_hien_tai' => 'Trạng thái hiện tại',
            'dat_kpi_dt_200k' => 'Đạt KPI DT (>=200k)',
            'ngay_xac_dinh_trang_thai' => 'Ngày xác định trạng thái',
            'hanh_dong_bat_buoc' => 'Hành động bắt buộc',
            'deadline_xu_ly' => 'Deadline xử lý',
            'canh_bao' => 'Cảnh báo',
            'bien_dong_t2_t1' => 'Biến động T2/T1',
            'bien_dong_t3_t1' => 'Biến động T3/T1',
        ];
    }
}
