<?php

namespace App\Policies;

use App\TimeEntry;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimeEntryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any time entries.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can('list.time_entries.self');
    }

    /**
     * Determine whether the user can view the time entry.
     *
     * @param  \App\User  $user
     * @param  \App\TimeEntry  $time_entry
     * @return mixed
     */
    public function view(User $user, TimeEntry $time_entry)
    {
        if ($time_entry->user_id != $user->id) {
            return $user->can('show.time_entries.others');
        }

        return $user->can('show.time_entries.self');
    }

    /**
     * Determine whether the user can create time entries.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create.time_entries');
    }

    /**
     * Determine whether the user can update the time entry.
     *
     * @param  \App\User  $user
     * @param  \App\TimeEntry  $time_entry
     * @return mixed
     */
    public function update(User $user, TimeEntry $time_entry)
    {
        if ($time_entry->locked_at != null) {
            return false;
        }

        if ($time_entry->user_id != $user->id) {
            return $user->can('edit.time_entries.others');
        }

        return $user->can('edit.time_entries.self');
    }

    /**
     * Determine whether the user can delete the time entry.
     *
     * @param  \App\User  $user
     * @param  \App\TimeEntry  $time_entry
     * @return mixed
     */
    public function delete(User $user, TimeEntry $time_entry)
    {
        if ($time_entry->locked_at != null) {
            return false;
        }

        if ($time_entry->user_id != $user->id) {
            return $user->can('delete.time_entries.others');
        }

        return $user->can('delete.time_entries.self');
    }

    /**
     * Determine whether the user can restore the time entry.
     *
     * @param  \App\User  $user
     * @param  \App\TimeEntry  $time_entry
     * @return mixed
     */
    public function restore(User $user, TimeEntry $time_entry)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the time entry.
     *
     * @param  \App\User  $user
     * @param  \App\TimeEntry  $time_entry
     * @return mixed
     */
    public function forceDelete(User $user, TimeEntry $time_entry)
    {
        //
    }
    
    /**
     * Determine whether the user can lock the time entry.
     *
     * @param  \App\User  $user
     * @param  \App\TimeEntry  $time_entry
     * @return mixed
     */
    public function lock(User $user, TimeEntry $time_entry)
    {
        if ($time_entry->user_id != $user->id) {
            return $user->can('lock.time_entries.others');
        }

        return $user->can('lock.time_entries.self');
    }
        
    /**
     * Determine whether the user can unlock the time entry.
     *
     * @param  \App\User  $user
     * @param  \App\TimeEntry  $time_entry
     * @return mixed
     */
    public function unlock(User $user, TimeEntry $time_entry)
    {
        if ($time_entry->user_id != $user->id) {
            return $user->can('unlock.time_entries.others');
        }

        return $user->can('unlock.time_entries.self');
    }
        
    /**
     * Determine whether the user can batch lock time entries.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function batchLock(User $user)
    {
        return $user->can('lock.time_entries.batch');
    }
        
    /**
     * Determine whether the user can batch unlock time entries.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function batchUnlock(User $user)
    {
        return $user->can('unlock.time_entries.batch');
    }
            
    /**
     * Determine whether the user can stop the time entry.
     *
     * @param  \App\User  $user
     * @param  \App\TimeEntry  $time_entry
     * @return mixed
     */
    public function stop(User $user, TimeEntry $time_entry)
    {
        if (!$time_entry->is_timer) {
            return false;
        }
        
        if ($time_entry->user_id != $user->id) {
            return $user->can('stop.time_entries.others');
        }
        
        return $user->can('stop.time_entries.self');
    }
}
