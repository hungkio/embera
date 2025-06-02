<?php

namespace App\Domain\MailSetting\Policies;

use App\Domain\Admin\Models\Admin;
use App\Domain\MailSetting\Models\MailSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class MailSettingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function viewAny(Admin $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function view(Admin $user)
    {
        return $user->hasPermissionTo('mail-settings.view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function create(Admin $user)
    {
        return $user->hasPermissionTo('mail-settings.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param MailSetting $mailSetting
     * @return mixed
     */
    public function update(Admin $user, MailSetting $mailSetting)
    {
        return $user->hasPermissionTo('mail-settings.update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param MailSetting $mailSetting
     * @return mixed
     */
    public function delete(Admin $user, MailSetting $mailSetting)
    {
        return $user->hasPermissionTo('mail-settings.delete');
    }

    public function send(Admin $user, MailSetting $mailSetting)
    {
        return $user->hasPermissionTo('mail-settings.send');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param MailSetting $mailSetting
     * @return mixed
     */
    public function restore(Admin $user, MailSetting $mailSetting)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param MailSetting $mailSetting
     * @return mixed
     */
    public function forceDelete(Admin $user, MailSetting $mailSetting)
    {
        //
    }
}
