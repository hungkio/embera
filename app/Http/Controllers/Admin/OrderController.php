<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTables\BannerDataTable;
use App\DataTables\OrderDataTable;
use App\Domain\Banner\Models\Banner;
use App\Http\Requests\Admin\BannerStoreRequest;
use App\Http\Requests\Admin\BannerUpdateRequest;
use App\Imports\OrderImport;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;


class OrderController
{
    use AuthorizesRequests;

    public function index(OrderDataTable $dataTable, Request $request)
    {
        $this->authorize('view', Banner::class);

        // Parse ngày từ date_range
        $dateRange = $request->get('date_range');
        if ($dateRange && str_contains($dateRange, ' - ')) {
            list($date_from, $date_to) = explode(' - ', $dateRange);
        } else {
            $date_from = now()->startOfMonth()->toDateString();
            $date_to = now()->endOfMonth()->toDateString();
        }

        // Gộp lại để dùng trong query và truyền view
        $request->merge([
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);

        // Danh sách cho dropdown filter
        $staffList = Order::distinct()->pluck('staff_name')->filter()->sort()->toArray();
        $shopTypeList = Order::distinct()->pluck('shop_type')->filter()->sort()->toArray();
        $shopNameList = Order::distinct()->pluck('shop_name')->filter()->sort()->toArray();
        $regionList = Order::distinct()->pluck('region')->filter()->sort()->toArray();
        $cityList = Order::distinct()->pluck('city')->filter()->sort()->toArray();

        // Clone query để thống kê
        $query = Order::query();

        $query->whereBetween('when_to_rent', [
            Carbon::parse($date_from)->startOfDay(),
            Carbon::parse($date_to)->endOfDay()
        ]);

        if ($request->filled('staff')) {
            $query->where('staff_name', $request->staff);
        }

        if ($request->filled('shop_type')) {
            $query->where('shop_type', $request->shop_type);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('shop_name')) {
            $query->where('shop_name', $request->shop_name);
        }

        // Clone query cho thống kê
        $statQuery = clone $query;
        $orders = $statQuery->get();

        $totalRevenue = $orders->sum(fn($o) => $o->order_bills_vnd);

        // Bảng theo shop
        $byShop = $orders->groupBy('shop_name')->map(function ($group) {
            $shop_name = $group->first()->shop_name;
            $revenue = $group->sum('order_bills_vnd');

            // Lọc các đơn có doanh thu > 0
            $validRows = $group->filter(fn($o) => $o->order_bills_vnd > 0 && $o->profit_sharing_to_dealer !== null);

            // Tính trung bình chia sẻ trên các đơn có doanh thu
            $profit_share_avg = $validRows->avg('profit_sharing_to_dealer');

            // Áp dụng quy tắc: nếu = 0 hoặc 0.9 thì chia sẻ 0%
            if (in_array($profit_share_avg, [0, 0.9]) || $profit_share_avg === null) {
                $sharing_percent = 0;
            } else {
                $sharing_percent = round((0.9 - $profit_share_avg) * 100, 1);
            }

            return [
                'shop_name' => $shop_name,
                'revenue' => $revenue,
                'sharing_percent' => $sharing_percent,
            ];
        })->values();

        // Bảng theo nhân viên
        $byStaff = $orders->groupBy('staff_name')->map(function ($group) {
            return [
                'staff_name' => $group->first()->staff_name,
                'revenue' => $group->sum(fn($o) => $o->order_bills_vnd),
            ];
        })->values();

        // Bảng theo ngày
        $byDate = $orders->groupBy(fn($o) => Carbon::parse($o->when_to_rent)->format('Y-m-d'))
            ->map(function ($group, $date) {
                return [
                    'date' => $date,
                    'count' => $group->count(),
                    'revenue' => $group->sum(fn($o) => $o->order_bills_vnd),
                ];
            })->sortKeys()->values();

        return $dataTable->with([
            'filters' => $request->only(['staff', 'shop_type', 'shop_name', 'region', 'city', 'date_from', 'date_to']),
        ])->render('admin.orders.index', [
            'staffList' => $staffList,
            'shopTypeList' => $shopTypeList,
            'shopNameList' => $shopNameList,
            'regionList' => $regionList,
            'cityList' => $cityList,
            'totalRevenue' => $totalRevenue,
            'byShop' => $byShop,
            'byStaff' => $byStaff,
            'byDate' => $byDate,
            'filters' => request()->only([
                'staff', 'shop_type', 'shop_name', 'region', 'city', 'date_from', 'date_to'
            ]),
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

    public function create(): View
    {
        $this->authorize('create', Banner::class);

        return view('admin.banners.create');
    }

    public function store(BannerStoreRequest $request)
    {
        $this->authorize('create', Banner::class);
        $banner = Banner::create($request->except('image'));
        if ($request->hasFile('image')) {
            $banner->addMedia($request->file('image'))->toMediaCollection('banner');
        }
        flash()->success(__('Banner ":model" đã được tạo thành công! ', ['model' => $banner->title]));

        logActivity($banner, 'create'); // log activity

        return intended($request, route('admin.banners.index'));
    }

    public function edit(Banner $banner): View
    {
        $this->authorize('update', $banner);

        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Banner $banner, BannerUpdateRequest $request)
    {
        $this->authorize('update', $banner);
        $banner->update($request->except('image'));
        if ($request->hasFile('image')) {
            $banner->addMedia($request->file('image'))->toMediaCollection('banner');
        }
        flash()->success(__('Banner ":model" đã được cập nhật thành công!', ['model' => $banner->title]));

        logActivity($banner, 'update'); // log activity

        return intended($request, route('admin.banners.index'));
    }

    public function destroy(Banner $banner)
    {
        $this->authorize('delete', $banner);

        if (\App\Enums\BannerState::Active == $banner->status) {
            return response()->json([
                'status' => 'error',
                'message' => __('Banner đang hoạt động không thể xoá!'),
            ]);
        }

        logActivity($banner, 'delete'); // log activity

        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => __('Banner đã được xóa thành công!'),
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $count_deleted = 0;
        $deletedRecord = Banner::whereIn('id', $request->input('id'))->get();
        foreach ($deletedRecord as $banner) {
            if (\App\Enums\BannerState::Active != $banner->status) {
                logActivity($banner, 'delete'); // log activity
                $banner->delete();
                $count_deleted++;
            }
        }
        return response()->json([
            'status' => true,
            'message' => __('Đã xóa ":count" banner thành công và ":count_fail" banner đang được sử dụng, không thể xoá',
                [
                    'count' => $count_deleted,
                    'count_fail' => count($request->input('id')) - $count_deleted,
                ]),
        ]);
    }

    public function changeStatus(Banner $banner, Request $request)
    {
        $this->authorize('update', $banner);

        $banner->update(['status' => $request->status]);

        logActivity($banner, 'update'); // log activity

        return response()->json([
            'status' => true,
            'message' => __('Trạng thái Banner đã được cập nhật thành công!'),
        ]);
    }

    public function bulkStatus(Request $request)
    {
        $total = Banner::whereIn('id', $request->id)->get();
        foreach ($total as $banner)
        {
            $banner->update(['status' => $request->status]);
            logActivity($banner, 'update'); // log activity
        }

        return response()->json([
            'status' => true,
            'message' => __(':count banner đã được cập nhật trạng thái thành công !', ['count' => $total->count()]),
        ]);
    }
}
