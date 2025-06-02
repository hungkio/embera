<?php

namespace App\Domain\Taxonomy\Policies;

use App\Domain\Taxonomy\Models\Taxonomy;
use App\Domain\Admin\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxonomyPolicy
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
        return $user->hasPermissionTo('taxonomies.view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function create(Admin $user): bool
    {
        return $user->hasPermissionTo('taxonomies.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Taxonomy $taxonomy
     * @return mixed
     */
    public function update(Admin $user, Taxonomy $taxonomy): bool
    {
        return $user->hasPermissionTo('taxonomies.update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Taxonomy $taxonomy
     * @return mixed
     */
    public function delete(Admin $user, Taxonomy $taxonomy): bool
    {
        return $user->hasPermissionTo('taxonomies.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function restore(Admin $user, Taxonomy $taxonomy)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Taxonomy $taxonomy
     * @return mixed
     */
    public function forceDelete(Admin $user, Taxonomy $taxonomy)
    {
        //
    }
}
