<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTables\BannerDataTable;
use App\Domain\Banner\Models\Banner;
use App\Http\Requests\Admin\BannerStoreRequest;
use App\Http\Requests\Admin\BannerUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController
{
    use AuthorizesRequests;

    public function index(BannerDataTable $dataTable)
    {
        $this->authorize('view', Banner::class);

        return $dataTable->render('admin.banners.index');
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
