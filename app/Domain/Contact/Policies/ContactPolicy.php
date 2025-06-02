<?php

namespace App\Domain\Contact\Policies;

use App\Domain\Admin\Models\Admin;
use App\Domain\Contact\Models\Contact;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy
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
        return $user->hasPermissionTo('contacts.view');
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
     * @param Contact $contact
     * @return mixed
     */
    public function update(Admin $user, Contact $contact)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Contact $contact
     * @return mixed
     */
    public function delete(Admin $user, Contact $contact)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Contact $contact
     * @return mixed
     */
    public function restore(Admin $user, Contact $contact)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Domain\Admin\Models\Admin $user
     * @param Contact $contact
     * @return mixed
     */
    public function forceDelete(Admin $user, Contact $contact)
    {
        //
    }
}
