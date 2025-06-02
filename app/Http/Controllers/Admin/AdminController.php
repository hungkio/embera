<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminBulkDeleteRequest;
use App\Http\Requests\Admin\AdminRequest;
use App\DataTables\AdminDataTable;
use App\Domain\Acl\Models\Role;
use App\Domain\Admin\Actions\AdminCreateAction;
use App\Domain\Admin\Actions\AdminUpdateAction;
use App\Domain\Admin\Actions\BulkDeleteAction;
use App\Domain\Admin\DTO\AdminData;
use App\Domain\Admin\Models\Admin;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController
{
    use AuthorizesRequests;

    public function index(AdminDataTable $dataTable)
    {
        $this->authorize('view', Admin::class);

        return $dataTable->render('admin.admins.index');
    }

    public function create(): View
    {
        $this->authorize('create', Admin::class);

        $roles = Role::with('translation')->get();

        return view('admin.admins.create', compact('roles'));
    }

    public function store(AdminRequest $request, AdminCreateAction $action): RedirectResponse
    {
        $this->authorize('create', Admin::class);

        $adminData = AdminData::fromRequest($request);

        $admin = $action->execute($adminData);

        flash()->success(__('Tài khoản quản trị ":model" đã được tạo thành công !', ['model' => $admin->email]));

        logActivity($admin, 'create'); // log activity

        return intended($request, route('admin.admins.index'));
    }

    public function edit(Admin $admin): View
    {
        $this->authorize('update', $admin);

        $roles = Role::with('translation')->get();

        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    public function update(Admin $admin, AdminRequest $request, AdminUpdateAction $action): RedirectResponse
    {
        $this->authorize('update', $admin);

        $adminData = AdminData::fromRequest($request);

        $action->execute($admin, $adminData);

        flash()->success(__('Lưu thông tin thành công'));

        logActivity($admin, 'update'); // log activity

        return redirect()->route('admin.admins.index');
    }

    public function destroy(Admin $admin): JsonResponse
    {
        $this->authorize('delete', $admin);

        logActivity($admin, 'delete'); // log activity

        $admin->delete();

        return response()->json([
            'status' => true,
            'message' => __('Tài khoản quản trị ":model" đã được xóa thành công !', ['model' => $admin->email]),
        ]);
    }

    public function bulkDelete(AdminBulkDeleteRequest $request, BulkDeleteAction $action): JsonResponse
    {
        $this->authorize('delete', Admin::class);

        $deletedRecord = $action->execute($request->input('id'));

        return response()->json([
            'status' => true,
            'message' => __('Đã xóa ":count" tài khoản', ['count' => $deletedRecord]),
        ]);
    }
}
