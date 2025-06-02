<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PageDataTable;
use App\Domain\Menu\Models\MenuItem;
use App\Domain\Page\Models\Page;
use App\Enums\PageState;
use App\Http\Requests\Admin\PageRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PageController
{
    use AuthorizesRequests;

    public function index(PageDataTable $dataTable)
    {
        $this->authorize('view', Page::class);
        return $dataTable->render('admin.pages.index');
    }

    public function create(): View
    {
        $this->authorize('create', Page::class);
        return view('admin.pages.create');
    }

    public function store(PageRequest $request)
    {
        $this->authorize('create', Page::class);

        $data = $request->all();
        $data['user_id'] = auth('admins')->user()->id;
        $page = Page::create($data);
        flash()->success(__('Trang ":model" đã tạo thành công !', ['model' => $page->title]));

        logActivity($page, 'create'); // log activity

        return intended($request, route('admin.pages.index'));
    }

    public function edit(Page $page): View
    {
        $this->authorize('update', $page);

        return view('admin.pages.edit', compact('page'));
    }

    public function update(Page $page, PageRequest $request)
    {
        $this->authorize('update', $page);

        $page->update($request->all());

        flash()->success(__('Trang ":model" đã cập nhật thành công !', ['model' => $page->title]));

        logActivity($page, 'update'); // log activity

        return intended($request, route('admin.pages.index'));
    }

    public function destroy(Page $page)
    {
        $this->authorize('delete', $page);
        if (\App\Enums\PageState::Active == $page->status || !$page->menu_items(MenuItem::TYPE_PAGE)->get()->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => __('Trang đang được sử dụng không thể xoá!'),
            ]);
        }

        logActivity($page, 'delete'); // log activity

        $page->delete();

        return response()->json([
           'success' => true,
           'message' => __('Trang đã xóa thành công !'),
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $count_deleted = 0;
        $deletedRecord = Page::whereIn('id', $request->input('id'))->get();
        foreach ($deletedRecord as $page) {
            if (\App\Enums\PageState::Active != $page->status && $page->menu_items(MenuItem::TYPE_PAGE)->get()->isEmpty()) {
                logActivity($page, 'delete'); // log activity
                $page->delete();
                $count_deleted++;
            }
        }
        return response()->json([
            'status' => true,
            'message' => __('Đã xóa ":count" trang thành công và ":count_fail" trang đang được sử dụng, không thể xoá',
                [
                    'count' => $count_deleted,
                    'count_fail' => count($request->input('id')) - $count_deleted,
                ]),
        ]);
    }

    public function changeStatus(Page $page, Request $request)
    {
        $this->authorize('update', $page);

        $page->update(['status' => $request->status]);

        logActivity($page, 'update'); // log activity

        return response()->json([
            'status' => true,
            'message' => __('Cập nhật trạng thái thành công !'),
        ]);
    }

    public function upLoadFileImage(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'file' => ['mimes:jpeg,jpg,png', 'required', 'max:2048'],
            ],
            [
                'file.mimes' => __('Tệp tải lên không hợp lệ !'),
                'file.max' => __('Tệp quá lớn !'),
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first('file'),
            ], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('file')->storePublicly('tmp/uploads');

        return response()->json([
            'file' => $file,
            'status' => true,
        ]);
    }
}
