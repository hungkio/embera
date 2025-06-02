<?php

declare(strict_types=1);

namespace App\Domain\Admin\Actions;

use App\Domain\Acl\Models\Role;
use App\Domain\Admin\DTO\AdminData;
use App\Domain\Admin\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminCreateAction
{
    public function execute(AdminData $adminData): Admin
    {
        $admin = new Admin;

        DB::transaction(function () use ($adminData, $admin) {
            $admin->first_name = $adminData->first_name;
            $admin->last_name = $adminData->last_name;
            $admin->email = $adminData->email;
            $admin->password = Hash::make($adminData->password);
            $admin->save();

            $role = Role::find($adminData->roles);
            $admin->assignRole($role);
        });

        return $admin;
    }
}
