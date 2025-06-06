<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTables\OrderDataTable;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OrderImport;

class OrderController
{
    use AuthorizesRequests;

    public function index(OrderDataTable $dataTable, Request $request)
    {
        $this->authorize('view', Order::class);

        $dateRange = $request->get('date_range');
        if ($dateRange && str_contains($dateRange, ' - ')) {
            list($date_from, $date_to) = explode(' - ', $dateRange);
        } else {
            $date_from = now()->startOfMonth()->toDateString();
            $date_to = now()->endOfMonth()->toDateString();
        }

        $request->merge([
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);

        // Danh sách dropdown filter mới
        $employeeList = Order::distinct()->pluck('employee_name')->filter()->sort()->toArray();
        $shopList = Order::distinct()->pluck('rental_shop')->filter()->sort()->toArray();
        $shopType = Order::distinct()->pluck('rental_shop_type')->filter()->sort()->toArray();
        $merchantList = Order::distinct()->pluck('merchant_name')->filter()->sort()->toArray();
        $regionList = Order::distinct()->pluck('region')->filter()->sort()->toArray();
        $cityList = Order::distinct()->pluck('city')->filter()->sort()->toArray();
        $areaList = Order::distinct()->pluck('area')->filter()->sort()->toArray();

        $query = Order::query()
            ->whereBetween('payment_time', [
                Carbon::parse($date_from)->startOfDay(),
                Carbon::parse($date_to)->endOfDay(),
            ]);

        if ($request->filled('employee_name')) {
            $query->where('employee_name', $request->employee_name);
        }

        if ($request->filled('rental_shop')) {
            $query->where('rental_shop', $request->rental_shop);
        }

        if ($request->filled('merchant_name')) {
            $query->where('merchant_name', $request->merchant_name);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }

        $orders = (clone $query)->orderByDesc('payment_time')->get();

        $totalRevenue = $orders->sum('order_amount');

        $byShop = $orders->groupBy('rental_shop')->map(function ($group) {
            $shop = $group->first()->rental_shop;
            $agent_share_ratio = $group->first()->agent_share_ratio;
            $revenue = $group->sum('order_amount');

//            $validRows = $group->filter(fn($o) => $o->order_amount > 0 && $o->agent_share_ratio !== null);
//            $avg_ratio = $validRows->avg('agent_share_ratio');
            $avg_ratio = $agent_share_ratio;

            $sharing = ($avg_ratio === null || in_array($avg_ratio, [0, 0.9])) ? 0 : round((0.9 - $avg_ratio) * 100, 1);

            return [
                'shop' => $shop,
                'revenue' => $revenue,
                'sharing_percent' => $sharing,
            ];
        })->values();

        $byEmployee = $orders->groupBy('employee_name')->map(function ($group) {
            return [
                'employee' => $group->first()->employee_name,
                'revenue' => $group->sum('order_amount'),
            ];
        })->values();

        $byDate = $orders->groupBy(fn($o) => Carbon::parse($o->payment_time)->format('Y-m-d'))
            ->map(function ($group, $date) {
                return [
                    'date' => $date,
                    'count' => $group->count(),
                    'revenue' => $group->sum('order_amount'),
                ];
            })->sortKeys()->values();

        return $dataTable->with([
            'filters' => $request->only(['employee_name', 'rental_shop', 'merchant_name', 'date_from', 'date_to']),
        ])->render('admin.orders.index', [
            'staffList' => $employeeList,
            'shopTypeList' => $shopType,
            'shopNameList' => $shopList,
            'merchantList' => $merchantList,
            'totalRevenue' => $totalRevenue,
            'regionList' => $regionList,
            'areaList' => $areaList,
            'cityList' => $cityList,
            'byShop' => $byShop,
            'byStaff' => $byEmployee,
            'byDate' => $byDate,
            'filters' => $request->only(['employee_name', 'rental_shop', 'merchant_name', 'date_from', 'date_to']),
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new OrderImport, $request->file('import_file'));
            return back()->with('success', 'Import thành công!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with('error', 'Import thất bại: ' . $e->getMessage());
        }
    }
}
