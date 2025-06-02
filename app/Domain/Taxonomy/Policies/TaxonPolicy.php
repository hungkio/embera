<?php

namespace App\Domain\Taxonomy\Policies;

use App\Domain\Taxonomy\Models\Taxon;
use App\Domain\Admin\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxonPolicy
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
        return $user->hasPermissionTo('taxons.view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function create(Admin $user): bool
    {
        return $user->hasPermissionTo('taxons.create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Taxon $taxon
     * @return mixed
     */
    public function update(Admin $user, Taxon $taxon): bool
    {
        return $user->hasPermissionTo('taxons.update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Taxon $taxon
     * @return mixed
     */
    public function delete(Admin $user, Taxon $taxon): bool
    {
        return $user->hasPermissionTo('taxons.delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function restore(Admin $user, Taxon $taxon)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Taxon $taxon
     * @return mixed
     */
    public function forceDelete(Admin $user, Taxon $taxon)
    {
        //
    }
}
