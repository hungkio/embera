<?php

declare(strict_types=1);

namespace App\Domain\Admin\Actions;

use App\Domain\Acl\Models\Role;
use App\Domain\Admin\DTO\AdminData;
use App\Domain\Admin\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUpdateAction
{
    public function execute(Admin $admin, AdminData $adminData): void
    {
        DB::transaction(function () use ($admin, $adminData) {
            $admin->first_name = $adminData->first_name;
            $admin->last_name = $adminData->last_name;
            $admin->email = $adminData->email;
            $admin->phone = $adminData->phone;
            if (! empty($adminData->password)) {
                $admin->password = Hash::make($adminData->password);
            }
            $admin->save();

            $role = Role::find($adminData->roles);
            $admin->syncRoles($role);
        });
    }
}
