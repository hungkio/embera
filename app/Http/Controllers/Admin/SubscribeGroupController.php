<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\SubscribeGroupDataTable;
use App\Domain\SubscribeEmail\Models\SubscribeEmail;
use App\Models\SubscribeGroup;
use App\Http\Requests\Admin\SubscribeGroupStoreRequest;
use App\Http\Requests\Admin\SubscribeGroupUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscribeGroupController
{

    public function index(SubscribeGroupDataTable $dataTable)
    {
        return $dataTable->render('admin.subscribe_groups.index');
    }

    public function create(): View
    {
        $emails = SubscribeEmail::all();
        return view('admin.subscribe_groups.create', compact('emails'));
    }

    public function store(SubscribeGroupStoreRequest $request)
    {
        $subscribe_group = SubscribeGroup::create([
            'name' => $request->name,
            'email_ids' => json_encode($request->email_ids)
        ]);

        flash()->success(__('Subscribe Group ":model" đã được tạo thành công! ', ['model' => $subscribe_group->name]));

        logActivity($subscribe_group, 'create'); // log activity

        return intended($request, route('admin.subs_group.index'));
    }

    public function edit(SubscribeGroup $sub_group): View
    {
        $emails = SubscribeEmail::all();

        return view('admin.subscribe_groups.edit', compact('sub_group', 'emails'));
    }

    public function update(SubscribeGroup $sub_group, SubscribeGroupUpdateRequest $request)
    {
        $sub_group->update($request->except('image'));
        if ($request->hasFile('image')) {
            $sub_group->addMedia($request->file('image'))->toMediaCollection('subscribe_group');
        }
        flash()->success(__('Subscribe Group ":model" đã được cập nhật thành công!', ['model' => $sub_group->name]));

        logActivity($sub_group, 'update'); // log activity

        return intended($request, route('admin.subs_group.index'));
    }

    public function destroy(SubscribeGroup $sub_group)
    {
        logActivity($sub_group, 'delete'); // log activity

        $sub_group->delete();

        return response()->json([
            'success' => true,
            'message' => __('Subscribe Group đã được xóa thành công!'),
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $count_deleted = 0;
        $deletedRecord = SubscribeGroup::whereIn('id', $request->input('id'))->get();
        foreach ($deletedRecord as $subscribe_group) {
            logActivity($subscribe_group, 'delete'); // log activity
            $subscribe_group->delete();
            $count_deleted++;
        }
        return response()->json([
            'status' => true,
            'message' => __('Đã xóa ":count" subscribe group thành công và ":count_fail" subscribe group đang được sử dụng, không thể xoá',
                [
                    'count' => $count_deleted,
                    'count_fail' => count($request->input('id')) - $count_deleted,
                ]),
        ]);
    }
}
