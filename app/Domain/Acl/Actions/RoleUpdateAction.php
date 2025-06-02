<?php

declare(strict_types=1);

namespace App\Domain\Acl\Actions;

use App\Domain\Acl\Models\Role;
use App\Domain\Admin\DTO\RoleData;
use Illuminate\Support\Facades\DB;

class RoleUpdateAction
{
    public function execute(Role $role, RoleData $roleData): void
    {
        DB::transaction(function () use ($role, $roleData) {
            $role->display_name = $roleData->display_name;
            $role->save();

            $role->syncPermissions($roleData->permissions);
        });
    }
}
