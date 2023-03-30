<?php

namespace App\Policies;

use App\User;
use App\WorkType;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any work types.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can('list.work_types.self');
    }

    /**
     * Determine whether the user can view the work type.
     *
     * @param  \App\User  $user
     * @param  \App\WorkType  $work_type
     * @return mixed
     */
    public function view(User $user, WorkType $work_type)
    {
        if ($work_type->role_id != null && $work_type->role_id != $user->roles->first()->id) {
            return $user->can('edit.work_types.others');
        }

        return $user->can('show.work_types.self');
    }

    /**
     * Determine whether the user can create work types.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create.work_types');
    }

    /**
     * Determine whether the user can update the work type.
     *
     * @param  \App\User  $user
     * @param  \App\WorkType  $work_type
     * @return mixed
     */
    public function update(User $user, WorkType $work_type)
    {
        if ($work_type->role_id != null && $work_type->role_id != $user->roles->first()->id) {
            return $user->can('edit.work_types.others');
        }

        return $user->can('edit.work_types.self');
    }

    /**
     * Determine whether the user can delete the work type.
     *
     * @param  \App\User  $user
     * @param  \App\WorkType  $work_type
     * @return mixed
     */
    public function delete(User $user, WorkType $work_type)
    {
        if (WorkType::whereNull('deleted_at')->count() < 2) {
            return false;
        }

        if ($work_type->role_id != null && $work_type->role_id != $user->roles->first()->id) {
            return $user->can('edit.work_types.others');
        }

        return $user->can('delete.work_types.self');
    }

    /**
     * Determine whether the user can restore the work type.
     *
     * @param  \App\User  $user
     * @param  \App\WorkType  $work_type
     * @return mixed
     */
    public function restore(User $user, WorkType $work_type)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the work type.
     *
     * @param  \App\User  $user
     * @param  \App\WorkType  $work_type
     * @return mixed
     */
    public function forceDelete(User $user, WorkType $work_type)
    {
        return false;
    }
}
