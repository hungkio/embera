<?php

namespace App\Domain\Menu\Policies;

use App\Domain\Admin\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Domain\Menu\Models\Menu;

class MenuPolicy
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
    public function view(Admin $user): bool
    {
        return $user->hasPermissionTo('menus.index');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function create(Admin $user): bool
    {
        return $user->hasPermissionTo('menus.store');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Menu $menu
     * @return mixed
     */
    public function update(Admin $user, Menu $menu): bool
    {
        return $user->hasPermissionTo('menus.edit');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Menu $menu
     * @return mixed
     */
    public function delete(Admin $user, Menu $menu): bool
    {
        return $user->hasPermissionTo('menus.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function restore(Admin $user, Menu $menu)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Menu $menu
     * @return mixed
     */
    public function forceDelete(Admin $user, Menu $menu)
    {
        //
    }
}
