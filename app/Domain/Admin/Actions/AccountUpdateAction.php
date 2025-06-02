<?php

declare(strict_types=1);

namespace App\Domain\Admin\Actions;

use App\Domain\Admin\DTO\AdminProfileData;
use App\Domain\Admin\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AccountUpdateAction
{
    public function execute(Admin $admin, AdminProfileData $adminData): void
    {
        $admin->first_name = $adminData->first_name;
        $admin->last_name = $adminData->last_name;
        $admin->email = $adminData->email;
        if (! empty($adminData->password)) {
            $admin->password = Hash::make($adminData->password);
        }
        $admin->save();

        if (! empty($adminData->avatar)) {
            $admin->addMedia($adminData->avatar)->toMediaCollection('avatar');
        }
    }
}
