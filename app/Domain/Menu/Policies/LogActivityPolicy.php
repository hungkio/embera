<?php

namespace App\Domain\Menu\Policies;

use App\Domain\Admin\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Activitylog\Models\Activity;

class LogActivityPolicy
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
        return $user->hasPermissionTo('log-activities.index');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function create(Admin $user): bool
    {
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Activity $activity
     * @return mixed
     */
    public function update(Admin $user, Activity $activity): bool
    {
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Activity $activity
     * @return mixed
     */
    public function delete(Admin $user, Activity $activity): bool
    {
        return $user->hasPermissionTo('log-activities.destroy');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Domain\Admin\Models\Admin  $user
     * @return mixed
     */
    public function restore(Admin $user, Activity $activity)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Activity $activity
     * @return mixed
     */
    public function forceDelete(Admin $user, Activity $activity)
    {
        //
    }
}
