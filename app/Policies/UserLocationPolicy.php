<?php

namespace App\Policies;

use App\User;
use App\UserLocation;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserLocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any user locations.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can('list.locations.self');
    }

    /**
     * Determine whether the user can view the user location.
     *
     * @param  \App\User  $user
     * @param  \App\UserLocation  $user_location
     * @return mixed
     */
    public function view(User $user, UserLocation $user_location)
    {
        if ($user_location->user_id != $user->id) {
            return $user->can('show.locations.others');
        }

        return $user->can('show.locations.self');
    }

    /**
     * Determine whether the user can create user locations.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create.locations');
    }

    /**
     * Determine whether the user can update the user location.
     *
     * @param  \App\User  $user
     * @param  \App\UserLocation  $user_location
     * @return mixed
     */
    public function update(User $user, UserLocation $user_location)
    {
        if ($user_location->user_id != $user->id) {
            return $user->can('edit.locations.others');
        }

        return $user->can('edit.locations.self');
    }

    /**
     * Determine whether the user can delete the user location.
     *
     * @param  \App\User  $user
     * @param  \App\UserLocation  $user_location
     * @return mixed
     */
    public function delete(User $user, UserLocation $user_location)
    {
        if (UserLocation::where('user_id', $user_location->user_id)->whereNull('deleted_at')->count() < 2) {
            return false;
        }

        if ($user_location->user_id != $user->id) {
            return $user->can('delete.locations.others');
        }

        return $user->can('delete.locations.self');
    }

    /**
     * Determine whether the user can restore the user location.
     *
     * @param  \App\User  $user
     * @param  \App\UserLocation  $user_location
     * @return mixed
     */
    public function restore(User $user, UserLocation $user_location)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the user location.
     *
     * @param  \App\User  $user
     * @param  \App\UserLocation  $user_location
     * @return mixed
     */
    public function forceDelete(User $user, UserLocation $user_location)
    {
        return false;
    }
}
