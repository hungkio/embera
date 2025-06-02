<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AccountUpdateRequest;
use App\Domain\Admin\Actions\AccountUpdateAction;
use App\Domain\Admin\DTO\AdminProfileData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountSettingController
{
    public function edit(): View
    {
        return view('admin.accountSetting');
    }

    public function update(AccountUpdateRequest $request, AccountUpdateAction $action): RedirectResponse
    {
        $adminData = AdminProfileData::fromRequest($request);

        $action->execute(Auth::user(), $adminData);

        flash()->success(__('Tài khoản đã được cập nhật thành công !'));

        return redirect()->back();
    }
}
