<?php

namespace App\Domain\Banner\Policies;

use App\Domain\Banner\Models\Banner;
use App\Domain\Admin\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class BannerPolicy
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
        return $user->hasPermissionTo('banners.view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function create(Admin $user)
    {
        return $user->hasPermissionTo('banners.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Role $role
     * @return mixed
     */
    public function update(Admin $user, Banner $banner)
    {
        return $user->hasPermissionTo('banners.update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Role $role
     * @return mixed
     */
    public function delete(Admin $user, Banner $banner)
    {
        return $user->hasPermissionTo('banners.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function restore(Admin $user, Role $role)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function forceDelete(Admin $user, Role $role)
    {
        //
    }
}
