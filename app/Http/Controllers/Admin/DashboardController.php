<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Page\Models\Page;
use App\Domain\Post\Models\Post;
use App\Models\Contract;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Shop;
use Carbon\Carbon;

class DashboardController
{
    public function index()
    {
        $totalMerchants = Merchant::count();

        // Define today and yesterday
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        // Calculate total income (no status filter to ensure data)
        $totalIncomeToday = Order::whereDate('created_at', $today)->sum('order_amount');
        $totalIncomeYesterday = Order::whereDate('created_at', $yesterday)->sum('order_amount');

        // Debug: Log order statuses and income data
        $orderStatuses = Order::distinct('order_status')->pluck('order_status')->toArray();
        \Log::info('Order Statuses:', $orderStatuses);
        \Log::info('Total Income Today:', ['value' => $totalIncomeToday]);
        \Log::info('Total Income Yesterday:', ['value' => $totalIncomeYesterday]);

        // Orders per hour in the last 24 hours
        $yesterday = now()->subDay()->startOfDay();
        $startTime = $yesterday;
        $endTime = $yesterday->clone()->endOfDay();

        $ordersPerHour = Order::whereBetween('rental_time', [$startTime, $endTime])
            ->selectRaw('HOUR(rental_time) as hour, COUNT(*) as order_count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('order_count', 'hour')
            ->toArray();

        $hourlyOrderData = array_fill(0, 24, 0);
        foreach ($ordersPerHour as $hour => $count) {
            $hourlyOrderData[$hour] = $count;
        }

        // Top 5 merchants by revenue this month and last month
        $now = now()->subMonth();
        $startOfMonth = $now->clone()->startOfMonth(); // Bắt đầu tháng hiện tại (04/08/2025 00:00:00)
        $startOfLastMonth = $now->clone()->subMonth()->startOfMonth(); // Bắt đầu tháng trước (01/07/2025 00:00:00)
        $endOfLastMonth = $now->clone()->subMonth()->endOfMonth(); // Kết thúc tháng trước (31/07/2025 23:59:59)

        // Fetch top 5 merchants for this month
        $topMerchantsThisMonth = Order::where('rental_time', '>=', $startOfMonth)
            ->whereNotNull('return_time') // Chỉ lấy đơn hàng đã trả
            ->where('return_time', '<=', now()) // Đảm bảo return_time không vượt quá hiện tại (10:43 AM +07, 06/08/2025)
            ->whereRaw('LOWER(order_status) = ?', ['complete'])
            ->leftJoin('shops', 'orders.rental_shop', '=', 'shops.shop_name')
            ->leftJoin('contracts', 'shops.contract_id', '=', 'contracts.id')
            ->selectRaw('orders.merchant_id as merchant_id') // Lấy merchant_id từ orders
            ->selectRaw('MAX(orders.merchant_name) as merchant_name') // Lấy merchant_name từ orders
            ->selectRaw('SUM(orders.order_amount) as total_revenue')
            ->groupBy('orders.merchant_id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get()
            ->map(function ($item) {
                // Xử lý NULL cho merchant_name
                $merchantName = $item->merchant_name ?? 'Unknown Merchant';
                return [
                    'id' => $item->merchant_id,
                    'name' => ucwords(strtolower($merchantName)),
                    'value' => (float) $item->total_revenue,
                ];
            })
            ->toArray();

        // Fetch revenue for the same merchants last month
        $merchantIds = array_column($topMerchantsThisMonth, 'id');
        $topMerchantsLastMonth = [];
        if (!empty($merchantIds)) {
            $topMerchantsLastMonth = Order::whereBetween('rental_time', [$startOfLastMonth, $endOfLastMonth])
                ->whereNotNull('return_time') // Chỉ lấy đơn hàng đã trả
                ->whereRaw('LOWER(order_status) = ?', ['complete'])
                ->whereIn('orders.merchant_id', $merchantIds) // Chỉ định rõ orders.merchant_id
                ->leftJoin('shops', 'orders.rental_shop', '=', 'shops.shop_name')
                ->leftJoin('contracts', 'shops.contract_id', '=', 'contracts.id')
                ->selectRaw('orders.merchant_id as merchant_id') // Lấy merchant_id từ orders
                ->selectRaw('MAX(orders.merchant_name) as merchant_name') // Lấy merchant_name từ orders
                ->selectRaw('SUM(orders.order_amount) as total_revenue')
                ->groupBy('orders.merchant_id')
                ->get()
                ->map(function ($item) {
                    // Xử lý NULL cho merchant_name
                    $merchantName = $item->merchant_name ?? 'Unknown Merchant';
                    return [
                        'id' => $item->merchant_id,
                        'name' => ucwords(strtolower($merchantName)),
                        'value' => (float) $item->total_revenue,
                    ];
                })
                ->toArray();
        }

        // Align last month's data with this month's merchants
        $lastMonthData = array_fill_keys(array_column($topMerchantsThisMonth, 'id'), 0.0);
        foreach ($topMerchantsLastMonth as $merchant) {
            $key = array_search($merchant['id'], array_column($topMerchantsThisMonth, 'id'));
            if ($key !== false) {
                $lastMonthData[$topMerchantsThisMonth[$key]['id']] = $merchant['value'];
            }
        }
        $topMerchantsLastMonth = array_values($lastMonthData);

        \Log::info('Top Merchants This Month:', ['data' => $topMerchantsThisMonth]);
        \Log::info('Top Merchants Last Month:', ['data' => $topMerchantsLastMonth]);

        // Number of merchants per month (last 12 months)
        $startDate = now()->subMonths(11)->startOfMonth(); // Bắt đầu từ 09/2024
        $endDate = now()->endOfMonth(); // Kết thúc 08/2025

        $merchantGrowthData = [];
        $cumulativeMerchantIds = []; // Lưu trữ tập hợp merchant_id tích lũy
        $allMonths = [];
        $currentMonth = clone $startDate;
        while ($currentMonth <= $endDate) {
            $allMonths[] = $currentMonth->format('Y-m');
            $currentMonth->addMonth();
        }

        foreach ($allMonths as $month) {
            // Lấy merchant_id mới trong tháng hiện tại
            $newMerchantsInMonth = Merchant::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])
                ->whereNotNull('id') // Đảm bảo có merchant_id
                ->distinct()
                ->pluck('id')
                ->toArray();

            // Thêm merchant_id mới vào tập hợp tích lũy
            $cumulativeMerchantIds = array_unique(array_merge($cumulativeMerchantIds, $newMerchantsInMonth));
            $merchantGrowthData[] = [
                'month' => $month,
                'merchant_count' => count($cumulativeMerchantIds),
            ];
        }

        $merchantGrowth = $merchantGrowthData;

        \Log::info('Merchant Growth Data (cumulative):', ['data' => $merchantGrowth]);

        // Number of users per month (last 12 months) based on cumulative unique user_id from orders
        $startDate = now()->subMonths(11)->startOfMonth(); // Bắt đầu từ 09/2024
        $endDate = now()->endOfMonth(); // Kết thúc 08/2025

        $allUserIds = Order::where('rental_time', '>=', $startDate)
            ->where('rental_time', '<=', $endDate)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        $userGrowthData = [];
        $cumulativeUserIds = []; // Lưu trữ tập hợp user_id tích lũy
        $allMonths = [];
        $currentMonth = clone $startDate;
        while ($currentMonth <= $endDate) {
            $allMonths[] = $currentMonth->format('Y-m');
            $currentMonth->addMonth();
        }

        foreach ($allMonths as $month) {
            // Lấy user_id mới trong tháng hiện tại
            $newUsersInMonth = Order::whereRaw("DATE_FORMAT(rental_time, '%Y-%m') = ?", [$month])
                ->whereNotNull('user_id')
                ->distinct()
                ->pluck('user_id')
                ->toArray();

            // Thêm user_id mới vào tập hợp tích lũy
            $cumulativeUserIds = array_unique(array_merge($cumulativeUserIds, $newUsersInMonth));
            $userGrowthData[] = [
                'month' => $month,
                'user_count' => count($cumulativeUserIds),
            ];
        }

        $userGrowth = $userGrowthData;

        \Log::info('User Growth Data (cumulative based on orders):', ['data' => $userGrowth]);

        // Kiểm tra dữ liệu thô để debug
        $rawOrders = Order::where('rental_time', '>=', $startDate)
            ->where('rental_time', '<=', $endDate)
            ->whereNotNull('rental_time')
            ->whereNotNull('user_id')
            ->select('rental_time', 'user_id')
            ->get();
        \Log::info('Raw Orders Data:', ['count' => $rawOrders->count(), 'data' => $rawOrders->toArray()]);

        // Define all possible shop types
        $shopTypes = [
            'Airport(机场)',
            'Arcade(游乐中心)',
            "Bar(酒吧)",
            'Beauty Salon/Tattoo Shop(美容院/纹身店)',
            'Coffee Shop(咖啡店)',
            'Casino(赌场)',
            'Hospital(医院)',
            'Night Club(夜店)',
            'Office Space(写字楼)',
            'Others(其它)',
            'Public Space(政府机构)',
            'Restaurant(餐馆)',
            'School(学校)'
        ];

        // Revenue by shop type in current month
        $revenueByShopTypeRaw = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('order_status', 'complete')
            ->select('rental_shop_type')
            ->selectRaw('SUM(order_amount) as total_revenue')
            ->groupBy('rental_shop_type')
            ->get()
            ->pluck('total_revenue', 'rental_shop_type')
            ->toArray();

        // Initialize revenue for all shop types with zero
        $revenueByShopType = array_fill_keys($shopTypes, 0);
        // Merge actual revenue, mapping null to 'Others(其它)'
        foreach ($revenueByShopTypeRaw as $shopType => $revenue) {
            $key = $shopType ?? 'Others(其它)';
            if (in_array($key, $shopTypes)) {
                $revenueByShopType[$key] = (float) $revenue;
            } else {
                $revenueByShopType['Others(其它)'] += (float) $revenue;
            }
        }

        // Format for ECharts
        $revenueByShopType = array_map(function ($shopType, $revenue) {
            return [
                'name' => $shopType,
                'value' => round((float) $revenue, 2),
            ];
        }, array_keys($revenueByShopType), $revenueByShopType);

        \Log::info('Revenue by Shop Type:', ['data' => $revenueByShopType]);

        // Average revenue per order for completed orders with positive amount
        $validOrders = Order::where('order_status', 'complete')
            ->where('order_amount', '>', 0) // Chỉ lấy đơn hàng có order_amount > 0
            ->get();
        $totalOrders = $validOrders->count();
        $totalRevenue = $validOrders->sum('order_amount');
        $avgRevenuePerOrder = $totalOrders > 0 ? round((float) ($totalRevenue / $totalOrders), 2) : 0.0;

        // Format for ECharts as a single pie slice
        $avgRevenuePerOrder = [
            [
                'name' => '',
                'value' => $avgRevenuePerOrder,
            ]
        ];

        \Log::info('Average Revenue Per Order (Completed with Positive Amount):', [
            'data' => $avgRevenuePerOrder,
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue
        ]);

        // Contract Growth by Day (from first contract to today, only days with contracts)
        $startDateRaw = Contract::active()->min('sign_date');
        $startDate = $startDateRaw ? Carbon::parse($startDateRaw)->startOfDay() : now()->startOfDay();
        $endDate = now()->endOfDay(); // 06/08/2025 10:43 AM +07

        $contracts = Contract::active()
            ->whereBetween('sign_date', [$startDate, $endDate])
            ->select('sign_date', 'status')
            ->get()
            ->groupBy(function ($contract) {
                return $contract->sign_date->format('Y-m-d');
            });

        $contractGrowth = [];
        $cumulativeBbnt = 0;
        $cumulativeNotSigned = 0;
        $cumulativeSigned = 0;

        foreach ($contracts as $date => $dayContracts) {
            $bbntCount = $dayContracts->where('status', Contract::BBNT)->count();
            $notSignedCount = $dayContracts->where('status', Contract::NOT_SIGN)->count();
            $signedCount = $dayContracts->where('status', Contract::SIGN)->count();

            $cumulativeBbnt += $bbntCount;
            $cumulativeNotSigned += $notSignedCount;
            $cumulativeSigned += $signedCount;

            $contractGrowth[] = [
                'date' => $date,
                'bbnt_count' => $cumulativeBbnt,
                'not_signed_count' => $cumulativeNotSigned,
                'signed_count' => $cumulativeSigned,
            ];
        }

        \Log::info('Contract Growth Data (only days with contracts):', ['data' => $contractGrowth]);

        // Average transactions per day this month
        $startOfMonth = now()->startOfMonth(); // 01/08/2025 00:00:00
        $endOfMonth = now()->endOfDay(); // 06/08/2025 10:59 AM +07
        $totalDays = $startOfMonth->diffInDays($endOfMonth) + 1; // Số ngày từ 1/8 đến 6/8

        $dailyTransactions = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as transaction_count')
            ->groupBy('date')
            ->get()
            ->pluck('transaction_count', 'date')
            ->toArray();

        $totalTransactions = array_sum($dailyTransactions);
        $avgTransactionsPerDay = $totalDays > 0 ? round((float) $totalTransactions / $totalDays, 2) : 0.0;

        $avgDailyTransactionsData = [
            'dates' => array_keys($dailyTransactions),
            'counts' => array_values($dailyTransactions),
            'average' => $avgTransactionsPerDay
        ];

        \Log::info('Average Daily Transactions Data:', ['data' => $avgDailyTransactionsData]);

        // Number of shops by shop type
        $shopTypesCount = Shop::select('shop_type')
            ->selectRaw('COUNT(*) as shop_count')
            ->groupBy('shop_type')
            ->get()
            ->pluck('shop_count', 'shop_type')
            ->toArray();

        // Initialize shop counts for all shop types with zero
        $shopsByShopType = array_fill_keys($shopTypes, 0);
        // Merge actual counts, mapping null to 'Others(其它)'
        foreach ($shopTypesCount as $shopType => $count) {
            $key = $shopType ?? 'Others(其它)';
            if (in_array($key, $shopTypes)) {
                $shopsByShopType[$key] = (int) $count;
            } else {
                $shopsByShopType['Others(其它)'] += (int) $count;
            }
        }

        // Format for ECharts
        $shopsByShopType = array_map(function ($shopType, $count) {
            return [
                'name' => $shopType,
                'value' => $count,
            ];
        }, array_keys($shopsByShopType), $shopsByShopType);

        \Log::info('Shops by Shop Type:', ['data' => $shopsByShopType]);

        $pageTops = Page::orderBy('view', 'desc')->take(5)->get();
        $postTops = Post::orderBy('view', 'desc')->take(5)->get();

        return view('admin.dashboards.dashboard', compact(
            'totalMerchants',
            'totalIncomeToday',
            'totalIncomeYesterday',
            'hourlyOrderData',
            'topMerchantsThisMonth',
            'topMerchantsLastMonth',
            'merchantGrowth',
            'userGrowth',
            'revenueByShopType',
            'avgRevenuePerOrder',
            'pageTops',
            'postTops',
            'contractGrowth',
            'avgDailyTransactionsData',
            'shopsByShopType'
        ));
    }

    public function genSiteMap()
    {
        $baseUrl = 'https://demo.kqbd.ai';

        $files = ['general.xml'];
        $files = array_merge($files, $this->genModel('App\Models\Country', '-football/', 'sitemap_country'));
        $files = array_merge($files, $this->genModel('App\Models\League', '-league/', 'sitemap_league'));
        $files = array_merge($files, $this->genModel('App\Models\Team', '', 'sitemap_team'));
        $files = array_merge($files, $this->genModel('App\Models\Country', '-football/national-team?', 'sitemap_nation'));
        $this->genGeneral();

        $sitemapIndex = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemapIndex .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($files as $file) {
            $sitemapIndex .= '<sitemap>';
            $sitemapIndex .= '<loc>' . $baseUrl . '/' . $file . '</loc>';
            $sitemapIndex .= '<lastmod>' . now()->tz('UTC')->toAtomString() . '</lastmod>';
            $sitemapIndex .= '</sitemap>';
        }

        $sitemapIndex .= '</sitemapindex>';

        file_put_contents(public_path("sitemap.xml"), $sitemapIndex);
    }

    public function genModel($model, $suffix, $filename)
    {
        $file = [];
        $chunkCount = 0;
        $model::chunk(100, function($countries) use (&$chunkCount, $suffix, $filename, &$file) {
            $chunkCount++;
            $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
            $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            foreach ($countries as $country) {
                if ($filename != 'sitemap_nation') {
                    if ($filename == 'sitemap_team') {
                        $sitemap .= $this->addLink($country->slug . $suffix . '/club');
                    } else {
                        $sitemap .= $this->addLink($country->slug . $suffix);
                    }
                }

                $sitemap .= $this->addLink($country->slug . $suffix . 'standings');
                $sitemap .= $this->addLink($country->slug . $suffix . 'top-scorers');
                $sitemap .= $this->addLink($country->slug . $suffix . 'results');
                $sitemap .= $this->addLink($country->slug . $suffix . 'fixtures');
                $sitemap .= $this->addLink($country->slug . $suffix . 'livescore');
                $sitemap .= $this->addLink($country->slug . $suffix . 'betting-odds');
                $sitemap .= $this->addLink($country->slug . $suffix . 'national-team');

                if ($filename == 'sitemap_country' || $filename == 'sitemap_league') {
                    $sitemap .= $this->addLink($country->slug . $suffix . 'predictions');
                }

                if ($filename == 'sitemap_country') {
                    $sitemap .= $this->addLink($country->slug . $suffix . 'analysis');
                }
            }

            $sitemap .= '</urlset>';

            $file[] = "$filename$chunkCount.xml";
            file_put_contents(public_path("$filename$chunkCount.xml"), $sitemap);
        });

        return $file;
    }

    public function genGeneral()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $sitemap .= $this->addLink('club');
        $sitemap .= $this->addLink('results');
        $sitemap .= $this->addLink('standings');
        $sitemap .= $this->addLink('top-scorers');
        $sitemap .= $this->addLink('fixtures');
        $sitemap .= $this->addLink('livescore');
        $sitemap .= $this->addLink('betting-odds');
        $sitemap .= $this->addLink('predictions');
        $sitemap .= $this->addLink('analysis');
        $sitemap .= $this->addLink('premium-tips');
        $sitemap .= $this->addLink('dropping-odds');
        $sitemap .= $this->addLink('fifa-rankings');
        $sitemap .= $this->addLink('rss');
        $sitemap .= $this->addLink('login');
        $sitemap .= $this->addLink('log-out');
        $sitemap .= $this->addLink('sign-up');
        $sitemap .= $this->addLink('favourite');
        $sitemap .= $this->addLink('404-not-found');
        $sitemap .= $this->addLink('about-us');
        $sitemap .= $this->addLink('terms-of-use');
        $sitemap .= $this->addLink('privacy-policy');
        $sitemap .= $this->addLink('feedback');
        $sitemap .= $this->addLink('link-exchange');
        $sitemap .= $this->addLink('ads');

        $sitemap .= '</urlset>';

        file_put_contents(public_path("general.xml"), $sitemap);
    }

    public function addLink($slug)
    {
        $baseUrl = 'https://demo.kqbd.ai';

        $sitemap = '<url>';
        $sitemap .= '<loc>' . $baseUrl . '/' . $slug . '</loc>';
        $sitemap .= '<lastmod>' . now()->tz('UTC')->toAtomString() . '</lastmod>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>0.8</priority>';
        $sitemap .= '</url>';

        return $sitemap;
    }
}
