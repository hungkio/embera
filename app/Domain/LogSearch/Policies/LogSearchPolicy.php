<?php

namespace App\Domain\LogSearch\Policies;

use App\Domain\Admin\Models\Admin;
use App\Domain\LogSearch\Models\LogSearch;
use Illuminate\Auth\Access\HandlesAuthorization;

class LogSearchPolicy
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
        return $user->hasPermissionTo('log-search.view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function create(Admin $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param LogSearch $logSearch
     * @return mixed
     */
    public function update(Admin $user, LogSearch $logSearch)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param LogSearch $logSearch
     * @return mixed
     */
    public function delete(Admin $user, LogSearch $logSearch)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param LogSearch $logSearch
     * @return mixed
     */
    public function restore(Admin $user, LogSearch $logSearch)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param LogSearch $logSearch
     * @return mixed
     */
    public function forceDelete(Admin $user, LogSearch $logSearch)
    {
        //
    }
}
