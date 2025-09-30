<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\Shop;
use App\Domain\Admin\Models\Admin;
use Illuminate\Support\Facades\DB;

class DashboardController
{
    public function index(Request $request)
    {
        // --- Lấy start_date và end_date từ request ---
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : now()->startOfMonth();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : now()->endOfDay();

        // Nếu start > end thì hoán đổi
        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        // --- KPI theo khoảng ngày ---
        $targetRevenue = setting('target_revenue', 0);

        $totalRevenue = Order::whereBetween('payment_time', [$startDate, $endDate])
            ->where('order_status', 'complete')
            ->sum('order_amount');

        $percentRevenue = $targetRevenue > 0 ? round(($totalRevenue / $targetRevenue) * 100, 2) : 0;
        $revenueRemaining = max(0, $targetRevenue - $totalRevenue);

        $avgOrderValue = Order::whereBetween('payment_time', [$startDate, $endDate])
            ->where('order_status', 'complete')
            ->avg('order_amount') ?? 0;

        $daysPassed = $startDate->diffInDays($endDate) + 1;
        $avgRevenuePerDay = $daysPassed > 0 ? round($totalRevenue / $daysPassed, 2) : 0;

        $avgRentalHours = Order::whereBetween('rental_time', [$startDate, $endDate])
            ->whereNotNull('return_time')
            ->where('order_status', 'complete')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, rental_time, return_time)) as avg_hours')
            ->value('avg_hours') ?? 0;

        // --- Previous period comparison ---
        $prevStartDate = $startDate->clone()->subDays($daysPassed);
        $prevEndDate   = $endDate->clone()->subDays($daysPassed);

        $prevTotalRevenue = Order::whereBetween('payment_time', [$prevStartDate, $prevEndDate])
            ->where('order_status', 'complete')
            ->sum('order_amount');

        $revenueChangePercent = $prevTotalRevenue > 0
            ? round((($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100, 2)
            : 0;

        // --- Prorated target percent ---
        $daysInMonth = now()->daysInMonth;
        $proratedPercent = round(($daysPassed / $daysInMonth) * 100, 2);
        $completionDiff = round($percentRevenue - $proratedPercent, 2);

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
        $topMerchantsThisMonth = Order::whereBetween('payment_time', [$startDate, $endDate])
            ->where('order_status', 'complete')
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
        $revenueByShopType = Order::whereBetween('payment_time', [$startDate, $endDate])
            ->where('order_status', 'complete')
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
        $dailyRevenue = Order::whereBetween('payment_time', [$startDate, $endDate])
            ->where('order_status', 'complete')
            ->selectRaw('DATE(payment_time) as date, SUM(order_amount) as revenue')
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
        $orderStats = Order::whereBetween('payment_time', [$startDate, $endDate])
            ->where('order_status', 'complete')
            ->selectRaw('DATE(payment_time) as date, COUNT(*) as order_count, AVG(order_amount) as avg_order_value')
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
        $revenueByRegion = Order::whereBetween('payment_time', [$startDate, $endDate])
            ->where('order_status', 'complete')
            ->select('region')
            ->selectRaw('SUM(order_amount) as total_revenue')
            ->groupBy('region')
            ->get()
            ->map(fn($row) => [
                'region' => $row->region ?? 'Unknown',
                'value' => (float) $row->total_revenue,
            ])
            ->toArray();

        $regions = array_column($revenueByRegion, 'region');
        $regionRevenues = array_column($revenueByRegion, 'value');

        // --- Top Shops ---
        $topShops = Order::select('rental_shop', \DB::raw('COUNT(*) as order_count, SUM(order_amount) as revenue'))
            ->where('order_status', 'complete')
            ->whereBetween('payment_time', [$startDate, $endDate])
            ->groupBy('rental_shop')
            ->orderByDesc('revenue')
            ->take(10)
            ->get()
            ->map(fn($item) => [
                'shop_name' => $item->rental_shop,
                'order_count' => $item->order_count,
                'revenue' => (float) $item->revenue
            ])
            ->toArray();

        $shopNamesTop = array_column($topShops, 'shop_name');
        $topOrderCounts = array_column($topShops, 'order_count');
        $topRevenues = array_column($topShops, 'revenue');

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

        // --- Revenue by Admin (BD) qua Contract -> Shop -> Order (map theo tên shop)
        $revenueByAdminRows = Admin::role('BD')
            ->with(['contracts.shops.ordersByName' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('payment_time', [$startDate, $endDate])
                  ->where('order_status', 'complete');
            }])
            ->get()
            ->map(function ($admin) {
                $totalRevenue = 0;
                foreach ($admin->contracts as $contract) {
                    foreach ($contract->shops as $shop) {
                        $totalRevenue += $shop->ordersByName->sum('order_amount');
                    }
                }
                return [
                    'admin_name'    => $admin->full_name,
                    'total_revenue' => $totalRevenue,
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();

        $adminRevenueNames   = $revenueByAdminRows->pluck('admin_name')->toArray();
        $adminRevenueValues  = $revenueByAdminRows->pluck('total_revenue')->map(fn($v) => (float)$v)->toArray();

        $sumAdminRevenue     = array_sum($adminRevenueValues);
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

        // --- Return view ---
        return view('admin.dashboards.dashboard', compact(
            // Dates
            'startDate', 'endDate',


            // KPI (filtered)
            'targetRevenue', 'totalRevenue', 'percentRevenue', 'revenueRemaining',
            'avgOrderValue', 'avgRevenuePerDay', 'avgRentalHours',
            'prevTotalRevenue', 'revenueChangePercent',
            'proratedPercent', 'completionDiff',

            // Contracts
            'activeContracts', 'expiringContractsCount', 'signedNotInstalled',

            // Merchants, Shops, Regions
            'topMerchantsThisMonth', 'revenueByShopType','shopTypePercents',
            'hourlyOrderData', 'dailyDates', 'dailyValues',
            'orderDates', 'orderCounts', 'avgOrderValues',
            'regions', 'regionRevenues',

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
}
