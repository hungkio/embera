<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Merchant;
use App\Models\DeviceStatus;
use App\Models\DeviceTurnOnHistory;
use App\Models\Order;
use App\Models\TblOrder;
use App\Models\TblShop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Shop;
use App\Domain\Admin\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController
{
    public function revenueDashboard(Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : Carbon::now('Asia/Ho_Chi_Minh')->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::now('Asia/Ho_Chi_Minh')->endOfDay();

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return view('admin.dashboards.revenue', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dailyStats' => $this->buildRevenueDailyStats($startDate, $endDate),
            'regionStats' => $this->buildRevenueRegionStats($startDate, $endDate),
            'topShopStats' => $this->buildRevenueTopShopStats($startDate, $endDate),
        ]);
    }

    public function deviceTurnOn(Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : Carbon::now('Asia/Ho_Chi_Minh')->subDays(6)->startOfDay();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::now('Asia/Ho_Chi_Minh')->endOfDay();

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $groupBy = in_array($request->get('group_by'), ['day', 'week', 'month'], true)
            ? $request->get('group_by')
            : 'day';

        return view('admin.dashboards.device-turn-on', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'groupBy' => $groupBy,
            'todayStats' => $this->buildDeviceTurnOnDashboard(),
            'rangeStats' => $this->buildDeviceTurnOnRangeDashboard($startDate, $endDate, $groupBy),
        ]);
    }

    public function index(Request $request)
    {
        // --- Lấy start_date và end_date từ request ---
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : now()->subDays(6)->startOfDay();   // mặc định 7 ngày gần nhất

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : now()->endOfDay();                 // đến hiện tại

        // Nếu start > end thì hoán đổi
        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        // --- KPI theo khoảng ngày ---
        $targetRevenue = setting('target_revenue', 0);
        $totalRevenue = Order::where('return_time', '>=' , $startDate)->where('return_time', '<=', $endDate)->sum('order_amount');
        $avgOrderValue = Order::whereBetween('return_time', [$startDate, $endDate])
            ->avg('order_amount') ?? 0;

        $daysPassed = $startDate->diffInDays($endDate) + 1;
        $avgRevenuePerDay = $daysPassed > 0 ? round($totalRevenue / $daysPassed, 2) : 0;

        $avgRentalHours = Order::whereBetween('rental_time', [$startDate, $endDate])
            ->whereNotNull('return_time')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, rental_time, return_time)) as avg_hours')
            ->value('avg_hours') ?? 0;

        // --- Previous period comparison ---
        $prevStartDate = $startDate->clone()->subDays($daysPassed);
        $prevEndDate   = $endDate->clone()->subDays($daysPassed);

        $prevTotalRevenue = Order::whereBetween('return_time', [$prevStartDate, $prevEndDate])
            ->sum('order_amount');

        $revenueChangePercent = $prevTotalRevenue > 0
            ? round((($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100, 2)
            : 0;

        // --- Hợp đồng ---
        $activeContracts = Contract::active()->where('expired_date', '>', now())->whereHas('shops')->count();
        $expiringContractsCount = Contract::active()->whereBetween('expired_date', [now(), now()->addDays(30)])->count();
        // Hợp đồng đã ký nhưng tất cả shop đều chưa lắp đặt (is_bound = false)
        $signedNotInstalled = Contract::where('status', Contract::SIGN)
            ->whereHas('shops') // có shop
            ->whereDoesntHave('shops', function ($q) {
                $q->where('is_bound', 1); // loại bỏ hợp đồng đã có shop nào lắp đặt
            })
            ->count();

        // --- Top Merchants ---
        $topMerchantsThisMonth = Order::whereBetween('return_time', [$startDate, $endDate])
            ->selectRaw('merchant_id, MAX(merchant_name) as merchant_name, SUM(order_amount) as total_revenue')
            ->groupBy('merchant_id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id'    => $item->merchant_id,
                    'name'  => $item->merchant_name ?? 'Unknown',
                    'value' => (float) $item->total_revenue,
                ];
            })
            ->toArray();

        // --- Revenue by Shop Type ---
        $revenueByShopType = Order::whereBetween('return_time', [$startDate, $endDate])
            ->select('rental_shop_type')
            ->selectRaw('SUM(order_amount) as total_revenue')
            ->groupBy('rental_shop_type')
            ->get()
            ->map(function ($row) {
                return [
                    'name'  => $row->rental_shop_type ?? 'Others',
                    'value' => (float) $row->total_revenue,
                ];
            })
            ->sortByDesc('value')   // (khuyến nghị) sắp giảm dần để nhìn trực quan hơn
            ->values()
            ->toArray();

        // --- Orders per hour (theo ngày kết thúc filter) ---
        $filterDate = $endDate->clone();
        $ordersPerHour = Order::whereBetween('rental_time', [$filterDate->clone()->startOfDay(), $filterDate])
            ->selectRaw('HOUR(rental_time) as hour, COUNT(*) as order_count')
            ->groupBy('hour')
            ->pluck('order_count', 'hour')
            ->toArray();

        $hourlyOrderData = array_fill(0, 24, 0);
        foreach ($ordersPerHour as $hour => $count) {
            $hourlyOrderData[$hour] = $count;
        }

        // Day map for Vietnamese weekdays
        $dayMap = [
            0 => 'CN', // Sunday
            1 => 'T2', // Monday
            2 => 'T3',
            3 => 'T4',
            4 => 'T5',
            5 => 'T6',
            6 => 'T7', // Saturday
        ];

        // --- Daily Revenue ---
        $dailyRevenue = Order::whereBetween('return_time', [$startDate, $endDate])
            ->selectRaw('DATE(return_time) as date, SUM(order_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($row) use ($dayMap) {
                $parsedDate = Carbon::parse($row->date);
                return [
                    'date' => $row->date,
                    'value' => (float) $row->revenue,
                    'label' => $parsedDate->format('d/m') . "\n" . $dayMap[$parsedDate->dayOfWeek],
                ];
            })
            ->toArray();

        $dailyDates = array_column($dailyRevenue, 'label');
        $dailyValues = array_column($dailyRevenue, 'value');

        // --- Order stats (count + avg order value) ---
        $orderStats = Order::whereBetween('return_time', [$startDate, $endDate])
            ->selectRaw('DATE(return_time) as date, COUNT(*) as order_count, AVG(order_amount) as avg_order_value')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($row) use ($dayMap) {
                $parsedDate = Carbon::parse($row->date);
                return [
                    'date' => $row->date,
                    'order_count' => (int) $row->order_count,
                    'avg_order_value' => (float) $row->avg_order_value,
                    'label' => $parsedDate->format('d/m') . "\n" . $dayMap[$parsedDate->dayOfWeek],
                ];
            })
            ->toArray();

        $orderDates = array_column($orderStats, 'label');
        $orderCounts = array_column($orderStats, 'order_count');
        $avgOrderValues = array_column($orderStats, 'avg_order_value');

        // --- Doanh thu theo khu vực ---
        // Map code -> tên đầy đủ
        $regionMap = [
            'HN'  => 'Hà Nội',
            'BN'  => 'Bắc Ninh',
            'HP'  => 'Hải Phòng',
            'DN'  => 'Đà Nẵng',
            'HCM' => 'TP Hồ Chí Minh',
        ];

        // Lấy đơn trong khoảng thời gian
        $ordersRegion = Order::whereBetween('return_time', [$startDate, $endDate])->get();

        // Gom doanh thu theo code (HN, BN, ...)
        $revenueByRegion = $ordersRegion->groupBy(function ($order) {
            $shop = $order->rental_shop ?? '';
            if (preg_match('/\(MB-([A-Z]{2})-/', $shop, $matches)) {
                return $matches[1]; // ví dụ: HN, BN
            }
            return 'Other';
        })->map(function ($group, $regionCode) {
            return [
                'region' => $regionCode,
                'value'  => $group->sum('order_amount'),
            ];
        });

        // ✅ Chỉ giữ lại HN và BN
        $revenueByRegion = $revenueByRegion->filter(function ($item) {
            return in_array($item['region'], ['HN', 'BN']);
        })->values()->toArray();

        // Map sang tên đầy đủ (nếu cần hiển thị label đẹp)
        $regions = array_map(function ($code) use ($regionMap) {
            return $regionMap[$code] ?? $code;
        }, array_column($revenueByRegion, 'region'));

        $regionRevenues = array_column($revenueByRegion, 'value');

        // --- Orders Stats by Region ---
        $orderStatsByRegion = Order::whereBetween('return_time', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($order) {
                // Lấy code khu vực từ rental_shop (ví dụ MB-HN-xxx)
                if (preg_match('/\(MB-([A-Z]{2})-/', $order->rental_shop ?? '', $matches)) {
                    return $matches[1]; // HN, BN, HCM...
                }
                return 'Other';
            })
            ->map(function ($orders, $regionCode) use ($dayMap) {
                return $orders->groupBy(function ($o) {
                    return Carbon::parse($o->return_time)->toDateString();
                })->map(function ($dayOrders, $date) use ($dayMap) {
                    $parsedDate = Carbon::parse($date);
                    return [
                        'date' => $date,
                        'label' => $parsedDate->format('d/m') . "\n" . $dayMap[$parsedDate->dayOfWeek],
                        'order_count' => $dayOrders->count(),
                        'total_value' => $dayOrders->sum('order_amount'),
                        'avg_value'   => $dayOrders->avg('order_amount') ?? 0,
                    ];
                })->values();
            });

        // Top shops
        $topShops = Order::select(
                'rental_shop',
                \DB::raw('COUNT(*) as order_count'),
                \DB::raw('SUM(order_amount) as revenue')
            )
            ->whereBetween('return_time', [$startDate, $endDate])
            ->groupBy('rental_shop')
            ->orderByDesc('revenue')
            ->take(10)
            ->get()
            ->map(fn($item) => [
                'shop_name'   => $item->rental_shop,
                'order_count' => (int) $item->order_count,
                'revenue'     => (float) $item->revenue,
            ])
            ->toArray();

        $shopNamesTop   = array_column($topShops, 'shop_name');
        $topOrderCounts = array_column($topShops, 'order_count');
        $topRevenues    = array_column($topShops, 'revenue');


        // Chuẩn bị mảng cho chart
        $shopNamesTop   = array_column($topShops, 'shop_name');
        $topOrderCounts = array_column($topShops, 'order_count');
        $topRevenues    = array_column($topShops, 'revenue');


        // Doanh thu theo mô hình kinh doanh
        $shopTypeNames = array_column($revenueByShopType, 'name');
        $shopTypeValues = array_column($revenueByShopType, 'value');

       // ✅ Tỷ trọng từng loại trong tổng = 100%
       $totalRevenueShopType = array_sum($shopTypeValues);
       $shopTypePercents = array_map(function ($v) use ($totalRevenueShopType) {
           return $totalRevenueShopType > 0 ? round(($v / $totalRevenueShopType) * 100, 2) : 0;
       }, $shopTypeValues);

        // Doanh thu theo khu vực (ECharts dataset)
        $regionSource = [];
        foreach ($regions as $i => $region) {
            $regionSource[] = [
                'region' => $region,
                'value'  => $regionRevenues[$i] ?? 0,
            ];
        }

        // Map region code -> tên
        $regionCodes = ['HN' => 'Hà Nội', 'BN' => 'Bắc Ninh'];

        // --- Orders Stats by Region (HN, BN) ---
        $orderStatsByRegionCharts = [];
        $ordersByRegion = Order::whereBetween('return_time', [$startDate, $endDate])
            ->whereNotNull('return_time') // tránh null
            ->get()
            ->groupBy(function ($order) {
                if ($order->rental_shop && preg_match('/\(MB-([A-Z]{2})-/', $order->rental_shop, $matches)) {
                    return $matches[1]; // HN, BN...
                }
                return null;
            })
            ->filter(fn($orders, $code) => in_array($code, ['HN','BN']));

        foreach ($regionCodes as $regionCode => $regionName) {
            $orders = $ordersByRegion->get($regionCode, collect());
            $daily  = $orders->groupBy(fn($o) => Carbon::parse($o->return_time)->toDateString());

            $labels = [];
            $counts = [];
            $totals = [];
            $avgPct = [];

            $totalRevenueRegion = (float) $orders->sum('order_amount');

            foreach ($daily as $date => $dayOrders) {
                $parsedDate = Carbon::parse($date);
                $labels[]   = $parsedDate->format('d/m');

                $counts[]   = (int) $dayOrders->count();
                $totals[]   = (float) $dayOrders->sum('order_amount');

                // tỷ lệ % đóng góp so với tổng khu vực
                $percent = $totalRevenueRegion > 0
                    ? round(($dayOrders->sum('order_amount') / $totalRevenueRegion) * 100, 2)
                    : 0;
                $avgPct[] = $percent;
            }

            $orderStatsByRegionCharts[$regionCode] = [
                'labels' => $labels,
                'counts' => $counts,
                'totals' => $totals,
                'avgPct' => $avgPct,
            ];
        }

        // --- Đơn hàng 0 đồng theo khu vực ---
        $zeroOrderStats = [];
        $ordersByRegionZero = Order::whereBetween('return_time', [$startDate, $endDate])
            ->whereNotNull('return_time')
            ->get()
            ->groupBy(function ($order) {
                if ($order->rental_shop && preg_match('/\(MB-([A-Z]{2})-/', $order->rental_shop, $matches)) {
                    return $matches[1]; // HN, BN
                }
                return null;
            })
            ->filter(fn($orders, $code) => in_array($code, ['HN','BN']));

        foreach ($regionCodes as $regionCode => $regionName) {
            $orders = $ordersByRegionZero->get($regionCode, collect());
            $daily  = $orders->groupBy(fn($o) => Carbon::parse($o->return_time)->toDateString());

            $labels      = [];
            $zeroCounts  = [];
            $zeroPercent = [];

            foreach ($daily as $date => $dayOrders) {
                $parsedDate = Carbon::parse($date);
                $labels[]   = $parsedDate->format('d/m');

                $countTotal = $dayOrders->count();
                $countZero  = $dayOrders->where('order_amount', 0)->count();

                $zeroCounts[]  = (int) $countZero;
                $zeroPercent[] = $countTotal > 0
                    ? round(($countZero / $countTotal) * 100, 2)
                    : 0;
            }

            $zeroOrderStats[$regionCode] = [
                'labels'      => $labels,
                'zeroCounts'  => $zeroCounts,
                'zeroPercent' => $zeroPercent,
            ];
        }

        // --- Contracts by BD Admin ---
        $contractsByAdmin = Contract::with('admin')
            ->selectRaw('admin_id, COUNT(*) as contract_count')
            ->groupBy('admin_id')
            ->orderByDesc('contract_count')
            ->get();

        // Chuẩn hóa data
        $adminNames = [];
        $contractCounts = [];
        foreach ($contractsByAdmin as $row) {
            $adminNames[] = $row->admin?->full_name ?? 'Unknown';
            $contractCounts[] = (int) $row->contract_count;
        }

        // % share-of-total
        $totalContracts = array_sum($contractCounts);
        $adminPercents = array_map(function ($count) use ($totalContracts) {
            return $totalContracts > 0 ? round(($count / $totalContracts) * 100, 2) : 0;
        }, $contractCounts);

        // --- Revenue by Admin (BD) từ bảng orders (giống OrderController) ---
        $orders = Order::query()
            ->whereBetween('return_time', [$startDate, $endDate])
            ->leftJoin('shops', 'shops.shop_name', '=', 'orders.rental_shop')
            ->select('orders.*', 'shops.share_rate_type', 'shops.share_rate')
            ->orderByDesc('return_time')
            ->get();

        $revenueByAdminRows = $orders->groupBy(fn($o) => $o->employee_name ?: 'Chưa gán BD')
            ->map(function ($group, $name) {
                return [
                    'admin_name'    => $name,
                    'total_revenue' => $group->sum('order_amount'),
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();

        // Chuẩn bị data cho chart
        $adminRevenueNames    = $revenueByAdminRows->pluck('admin_name')->toArray();
        $adminRevenueValues   = $revenueByAdminRows->pluck('total_revenue')->map(fn($v) => (float)$v)->toArray();

        $sumAdminRevenue      = array_sum($adminRevenueValues);
        $adminRevenuePercents = array_map(
            fn($v) => $sumAdminRevenue > 0 ? round($v / $sumAdminRevenue * 100, 2) : 0,
            $adminRevenueValues
        );


        // --- Hợp đồng sắp hết hạn trong 30 ngày tới theo từng BD ---
        $expiringContracts = Contract::with('admin')
            ->active()
            ->whereBetween('expired_date', [now(), now()->addDays(30)])
            ->get();

        // Gom nhóm theo admin_id
        $expiringByAdmin = $expiringContracts
            ->groupBy('admin_id')
            ->map(function ($group) {
                return [
                    'admin_name'     => optional($group->first()->admin)->full_name ?? 'Unknown',
                    'expiring_count' => $group->count(),
                ];
            });

        // Tổng số HĐ sắp hết hạn
        $totalExpiring = $expiringByAdmin->sum('expiring_count');

        // Tính tỷ lệ %
        $expiringByAdmin = $expiringByAdmin->map(function ($row) use ($totalExpiring) {
            $row['percent'] = $totalExpiring > 0
                ? round(($row['expiring_count'] / $totalExpiring) * 100, 2)
                : 0;
            return $row;
        })->values();

        // --- Revenue Growth by Month (12 tháng gần nhất) ---
        $monthlyRevenue = Order::selectRaw('DATE_FORMAT(return_time, "%Y-%m") as month, SUM(order_amount) as total')
            ->whereNotNull('return_time')
            ->where('return_time', '>=', now()->subMonths(11)->startOfMonth()) // 12 tháng gần nhất
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                return [
                    'month' => Carbon::parse($row->month . '-01')->format('m/Y'),
                    'total' => (float) $row->total,
                ];
            })
            ->toArray();

        $months = array_column($monthlyRevenue, 'month');
        $monthlyTotals = array_column($monthlyRevenue, 'total');

        // --- Thống kê số lượng máy đã lắp / chưa lắp ---

        $deviceTurnOnStats = $this->buildDeviceTurnOnDashboard();

        $totalBoundDevices = $deviceTurnOnStats['total']['assigned'];
        $devices = DeviceStatus::count();
        $device_online = $deviceTurnOnStats['total']['online'];
        $device_offline = $deviceTurnOnStats['total']['offline'];

        $totalUnboundDevices = max(0, $devices - $totalBoundDevices);

        $merchants = Merchant::count();

        $totalOrder = Order::where('return_time', '>=' , $startDate)->where('return_time', '<=', $endDate)->count();
        $totalErrorOrder = TblOrder::where('created_at', '>=' , $startDate)->where('created_at', '<=', $endDate)->whereNull('battery_code')->count();
        $totalErrorRefund = TblOrder::where('created_at', '>=' , $startDate)->where('created_at', '<=', $endDate)->where('note', 'like', 'FT%')->orWhere('status', 'refund_failed')->count();

        // --- Return view ---
        return view('admin.dashboards.dashboard', compact(
            // Dates
            'startDate', 'endDate','months', 'monthlyTotals',


            // KPI (filtered)
            'targetRevenue', 'totalRevenue',
            'avgOrderValue', 'avgRevenuePerDay', 'avgRentalHours',
            'prevTotalRevenue', 'revenueChangePercent', 'totalBoundDevices','totalUnboundDevices',
            'device_online', 'device_offline', 'deviceTurnOnStats', 'merchants', 'totalErrorOrder', 'totalOrder', 'totalErrorRefund',

            // Contracts
            'activeContracts', 'expiringContractsCount', 'signedNotInstalled',

            // Merchants, Shops, Regions
            'topMerchantsThisMonth', 'revenueByShopType','shopTypePercents',
            'hourlyOrderData', 'dailyDates', 'dailyValues',
            'orderDates', 'orderCounts', 'avgOrderValues',
            'regions', 'regionRevenues','orderStatsByRegionCharts','zeroOrderStats',

            // Top shops
            'shopNamesTop', 'topOrderCounts', 'topRevenues',
            'shopTypeNames',
            'shopTypeValues',
            'regionSource',
            'adminNames', 'contractCounts', 'adminPercents',
            'adminRevenueNames', 'adminRevenueValues', 'adminRevenuePercents',

            // Expiring contracts
            'expiringByAdmin','totalExpiring'
        ));
    }

    private function buildDeviceTurnOnDashboard(): array
    {
        $today = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
        $records = $this->getDeviceTurnOnRecordsForDate($today);

        return $this->summarizeDeviceTurnOnGroups($records) + ['date' => $today];
    }

    private function getDeviceTurnOnRecordsForDate(string $date)
    {
        $records = collect();

        if (Schema::hasTable('device_turn_on_histories')) {
            $records = DeviceTurnOnHistory::query()
                ->with('shop')
                ->whereDate('recorded_date', $date)
                ->get();
        }

        if ($date !== Carbon::now('Asia/Ho_Chi_Minh')->toDateString()) {
            return $records;
        }

        if ($records->isEmpty()) {
            $records = DeviceStatus::query()
                ->with('shop')
                ->get()
                ->map(function (DeviceStatus $device) {
                    return (object) [
                        'shop_code' => $device->shop_code,
                        'status' => $device->status,
                        'shop' => $device->shop,
                    ];
                });
        }

        return $records;
    }

    private function summarizeDeviceTurnOnGroups($records): array
    {
        $assignedRecords = $records->filter(fn ($record) => $this->isAssignedDevice($record));
        $hanoiRecords = $assignedRecords->filter(fn ($record) => $this->isHanoiDevice($record));
        $provinceRecords = $assignedRecords->reject(fn ($record) => $this->isHanoiDevice($record));

        return [
            'total' => $this->summarizeDeviceTurnOnRecords($assignedRecords),
            'hanoi' => $this->summarizeDeviceTurnOnRecords($hanoiRecords),
            'province' => $this->summarizeDeviceTurnOnRecords($provinceRecords),
        ];
    }

    private function buildDeviceTurnOnRangeDashboard(Carbon $startDate, Carbon $endDate, string $groupBy = 'day'): array
    {
        $buckets = [];
        $today = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
        $recordsByDate = collect();

        if (Schema::hasTable('device_turn_on_histories')) {
            $recordsByDate = DeviceTurnOnHistory::query()
                ->with('shop')
                ->whereBetween('recorded_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get()
                ->groupBy(fn ($record) => $record->recorded_date->toDateString());
        }

        $cursor = $startDate->copy()->startOfDay();
        $lastDate = $endDate->copy()->startOfDay();

        while ($cursor->lte($lastDate)) {
            $date = $cursor->toDateString();
            $records = $recordsByDate->get($date, collect());

            if ($date === $today && $records->isEmpty()) {
                $records = $this->getDeviceTurnOnRecordsForDate($date);
            }

            $stats = $this->summarizeDeviceTurnOnGroups($records);
            [$bucketKey, $bucketLabel] = $this->getDeviceTurnOnBucket($cursor, $groupBy);

            if (!isset($buckets[$bucketKey])) {
                $buckets[$bucketKey] = [
                    'label' => $bucketLabel,
                    'total' => ['online' => 0, 'offline' => 0, 'assigned' => 0],
                    'hanoi' => ['online' => 0, 'offline' => 0, 'assigned' => 0],
                    'province' => ['online' => 0, 'offline' => 0, 'assigned' => 0],
                ];
            }

            foreach (['total', 'hanoi', 'province'] as $scope) {
                $buckets[$bucketKey][$scope]['assigned'] += $stats[$scope]['assigned'];
                $buckets[$bucketKey][$scope]['online'] += $stats[$scope]['online'];
                $buckets[$bucketKey][$scope]['offline'] += $stats[$scope]['offline'];
            }

            $cursor->addDay();
        }

        $labels = array_column($buckets, 'label');
        $series = [
            'total' => ['online' => [], 'offline' => [], 'assigned' => []],
            'hanoi' => ['online' => [], 'offline' => [], 'assigned' => []],
            'province' => ['online' => [], 'offline' => [], 'assigned' => []],
        ];

        foreach ($buckets as $bucket) {
            foreach (['total', 'hanoi', 'province'] as $scope) {
                $series[$scope]['assigned'][] = $bucket[$scope]['assigned'];
                $series[$scope]['online'][] = $bucket[$scope]['online'];
                $series[$scope]['offline'][] = $bucket[$scope]['offline'];
            }
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    private function getDeviceTurnOnBucket(Carbon $date, string $groupBy): array
    {
        if ($groupBy === 'week') {
            $startOfWeek = $date->copy()->startOfWeek();
            $endOfWeek = $date->copy()->endOfWeek();

            return [
                $startOfWeek->toDateString(),
                $startOfWeek->format('d/m') . ' - ' . $endOfWeek->format('d/m'),
            ];
        }

        if ($groupBy === 'month') {
            return [
                $date->format('Y-m'),
                $date->format('m/Y'),
            ];
        }

        return [
            $date->toDateString(),
            $date->format('d/m'),
        ];
    }

    private function buildRevenueDailyStats(Carbon $startDate, Carbon $endDate): array
    {
        $rows = Order::query()
            ->whereBetween('return_time', [$startDate, $endDate])
            ->selectRaw('DATE(return_time) as date, SUM(order_amount) as revenue, COUNT(*) as order_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $revenues = [];
        $orderCounts = [];

        $cursor = $startDate->copy()->startOfDay();
        $lastDate = $endDate->copy()->startOfDay();

        while ($cursor->lte($lastDate)) {
            $date = $cursor->toDateString();
            $row = $rows->get($date);

            $labels[] = $cursor->format('d/m');
            $revenues[] = (float) ($row->revenue ?? 0);
            $orderCounts[] = (int) ($row->order_count ?? 0);

            $cursor->addDay();
        }

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'orderCounts' => $orderCounts,
        ];
    }

    private function buildRevenueRegionStats(Carbon $startDate, Carbon $endDate): array
    {
        $rows = Order::query()
            ->whereBetween('return_time', [$startDate, $endDate])
            ->selectRaw('DATE(return_time) as date')
            ->selectRaw("SUM(CASE WHEN rental_shop LIKE '%MB-HN-%' OR rental_shop_id LIKE '%MB-HN-%' THEN order_amount ELSE 0 END) as hanoi_revenue")
            ->selectRaw("SUM(CASE WHEN (rental_shop NOT LIKE '%MB-HN-%' OR rental_shop IS NULL) AND (rental_shop_id NOT LIKE '%MB-HN-%' OR rental_shop_id IS NULL) THEN order_amount ELSE 0 END) as province_revenue")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $hanoi = [];
        $province = [];

        $cursor = $startDate->copy()->startOfDay();
        $lastDate = $endDate->copy()->startOfDay();

        while ($cursor->lte($lastDate)) {
            $date = $cursor->toDateString();
            $row = $rows->get($date);

            $labels[] = $cursor->format('d/m');
            $hanoi[] = (float) ($row->hanoi_revenue ?? 0);
            $province[] = (float) ($row->province_revenue ?? 0);

            $cursor->addDay();
        }

        return [
            'labels' => $labels,
            'hanoi' => $hanoi,
            'province' => $province,
        ];
    }

    private function buildRevenueTopShopStats(Carbon $startDate, Carbon $endDate): array
    {
        $rows = Order::query()
            ->whereBetween('return_time', [$startDate, $endDate])
            ->whereNotNull('rental_shop_id')
            ->where('rental_shop_id', '!=', '')
            ->select('rental_shop_id')
            ->selectRaw('MAX(rental_shop) as shop_name')
            ->selectRaw('SUM(order_amount) as revenue')
            ->selectRaw('COUNT(*) as order_count')
            ->groupBy('rental_shop_id')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return [
            'labels' => $rows->map(fn ($row) => $row->rental_shop_id)->all(),
            'shopNames' => $rows->map(fn ($row) => $row->shop_name ?: $row->rental_shop_id)->all(),
            'revenues' => $rows->map(fn ($row) => (float) $row->revenue)->all(),
            'orderCounts' => $rows->map(fn ($row) => (int) $row->order_count)->all(),
        ];
    }

    private function summarizeDeviceTurnOnRecords($records): array
    {
        $assigned = $records->count();
        $online = $records->where('status', 'online')->count();
        $offline = max(0, $assigned - $online);

        return [
            'assigned' => $assigned,
            'online' => $online,
            'offline' => $offline,
            'online_rate' => $assigned > 0 ? round(($online / $assigned) * 100, 2) : 0,
            'offline_rate' => $assigned > 0 ? round(($offline / $assigned) * 100, 2) : 0,
        ];
    }

    private function isAssignedDevice($record): bool
    {
        return !empty($record->shop_code);
    }

    private function isHanoiDevice($record): bool
    {
        $shop = $record->shop ?? null;
        $shopName = $shop->name ?? '';
        $shopCode = $shop->code ?? '';

        return stripos($shopName, 'MB-HN-') !== false
            || stripos($shopCode, 'MB-HN-') !== false;
    }
}
