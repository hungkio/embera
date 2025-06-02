<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\LogActivityDataTable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class LogActivityController
{
    use AuthorizesRequests;

    public function index(LogActivityDataTable $dataTable)
    {
        $this->authorize('view', Activity::class);

        return $dataTable->render('admin.log_activities.index');
    }

    public function show($id)
    {
        $activity = Activity::findOrFail($id);
        return view('admin.log_activities.detail', compact('activity'));
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $this->authorize('delete', $activity);

        $activity->delete();

        return response()->json([
           'success' => true,
           'message' => __('Đã xóa lịch sử thành công !'),
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $deletedRecord = Activity::whereIn('id', $request->input('id'))->delete();

        return response()->json([
            'status' => true,
            'message' => __('Đã xóa ":count" records', ['count' => $deletedRecord]),
        ]);
    }
}
