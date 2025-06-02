<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Admin\Models\Admin;
use App\Http\Requests\Admin\RoleBulkDeleteRequest;
use App\Http\Requests\Admin\RoleRequest;
use App\DataTables\RoleDataTable;
use App\Domain\Acl\Actions\RoleBulkDeleteAction;
use App\Domain\Acl\Actions\RoleCreateAction;
use App\Domain\Acl\Actions\RoleUpdateAction;
use App\Domain\Acl\Models\Permission;
use App\Domain\Acl\Models\Role;
use App\Domain\Admin\DTO\RoleData;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoleController
{
    use AuthorizesRequests;

    public function index(RoleDataTable $dataTable)
    {
        $this->authorize('view', Role::class);

        return $dataTable->render('admin.roles.index');
    }

    public function create(): View
    {
        $this->authorize('create', Role::class);

        $permissions = Permission::with('translation')->get();
        $groupPermissions = $permissions->groupBy(function ($permission) {
            [$group] = explode('.', $permission->name);

            return $group;
        });

        return view('admin.roles.create', compact('groupPermissions'));
    }

    public function store(RoleRequest $request, RoleCreateAction $action): RedirectResponse
    {
        $this->authorize('create', Role::class);

        $roleData = RoleData::fromRequest($request);

        $role = $action->execute($roleData);

        flash()->success(__('Phân quyền ":model" đã được tạo thành công !', ['model' => $role->display_name]));

        logActivity($role, 'create'); // log activity

        return intended($request, route('admin.roles.edit', $role));
    }

    public function edit(Role $role): View
    {
        $this->authorize('update', $role);

        $allowPermissions = $role->getPermissionNames()->toArray();
        $permissions = Permission::with('translation')->get();
        $groupPermissions = $permissions->groupBy(function ($permission) {
            [$group] = explode('.', $permission->name);

            return $group;
        });

        return view('admin.roles.edit', compact('allowPermissions', 'role', 'groupPermissions'));
    }

    public function update(Role $role, RoleRequest $request, RoleUpdateAction $action): RedirectResponse
    {
        $this->authorize('update', $role);

        $roleData = RoleData::fromRequest($request);

        $action->execute($role, $roleData);

        flash()->success(__('Lưu thông tin thành công'));

        logActivity($role, 'update'); // log activity

        return intended($request, route('admin.roles.edit', $role));
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->authorize('delete', $role);

        // check members of role
        $admins = $this->getMembers($role->id);
        if (!$admins->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => __('Quyền đã được thiết lập, không thể xóa!'),
            ]);
        }

        logActivity($role, 'delete'); // log activity

        $role->delete();

        return response()->json([
            'status' => true,
            'message' => __('Phân quyền ":model" đã được xóa thành công !', ['model' => $role->display_name]),
        ]);
    }

    public function bulkDelete(RoleBulkDeleteRequest $request, RoleBulkDeleteAction $action): JsonResponse
    {
        $ids = [];
        foreach ($request->input('id') as $id) {
            $members = $this->getMembers($id);
            if ($members->isEmpty()) {
                $ids[] = $id;
            }
        }
        $deletedRecord = $action->execute($ids);

        return response()->json([
            'status' => true,
            'message' => __('Đã xóa ":count" phân quyền thành công và ":count_fail" phân quyền đã được thiết lập, không thể xóa',
                [
                    'count' => $deletedRecord,
                    'count_fail' => count($request->input('id')) - $deletedRecord,
                ]),
        ]);
    }

    public function getMembers($role_id)
    {
        $member_ids = DB::table('model_has_roles')->where('role_id', $role_id)->get();
        if ($member_ids) {
            $member_ids = $member_ids->keyBy('model_id')->toArray();
            $member_ids = array_keys($member_ids);
        }
        return Admin::whereIn('id', $member_ids)->get();
    }
}
