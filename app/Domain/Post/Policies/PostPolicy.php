<?php

namespace App\Domain\Post\Policies;

use App\Domain\Post\Models\Post;
use App\Domain\Admin\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
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
        return $user->hasPermissionTo('posts.view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function create(Admin $user)
    {
        return $user->hasPermissionTo('posts.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Role $role
     * @return mixed
     */
    public function update(Admin $user, Post $post)
    {
        return $user->hasPermissionTo('posts.update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Role $role
     * @return mixed
     */
    public function delete(Admin $user, Post $post)
    {
        return $user->hasPermissionTo('posts.delete');
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
