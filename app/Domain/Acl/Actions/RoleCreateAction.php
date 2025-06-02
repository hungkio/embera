<?php

declare(strict_types=1);

namespace App\Domain\Acl\Actions;

use App\Domain\Acl\Models\Role;
use App\Domain\Admin\DTO\RoleData;
use Illuminate\Support\Facades\DB;

class RoleCreateAction
{
    public function execute(RoleData $roleData): Role
    {
        $role = new Role;
        DB::transaction(function () use ($role, $roleData) {
            $role->name = $roleData->name;
            $role->display_name = $roleData->display_name;
            $role->save();

            $role->givePermissionTo($roleData->permissions);
        });

        return $role;
    }
}
