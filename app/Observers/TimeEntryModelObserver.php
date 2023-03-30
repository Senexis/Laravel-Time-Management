<?php

namespace App\Observers;

use App\TimeEntry;
use Illuminate\Support\Facades\Auth;

class TimeEntryModelObserver
{
    public function retrieved($model)
    {
        $dispatcher = TimeEntry::getEventDispatcher();
        TimeEntry::unsetEventDispatcher();

        if (
            $model->time_worked == 0 && $model->is_timer != 1 &&
            $model->start_time != null && $model->end_time != null &&
            $model->pause_time == null && $model->resume_time == null
        ) {
            $model->time_worked = $model->end_time->diffInSeconds($model->start_time);
            $model->save();
        }

        TimeEntry::setEventDispatcher($dispatcher);
    }

    public function creating($model)
    {
        $dispatcher = TimeEntry::getEventDispatcher();
        TimeEntry::unsetEventDispatcher();

        // Non-timer time entries
        if ($model->end_time != null) {
            $model->time_worked = $model->end_time->diffInSeconds($model->start_time);
        }

        // Database seeders use this.
        if (!Auth::check()) return;

        // Update the last fields if they are different.
        $user = Auth::user();
        $save_user = false;

        if ($model->project_id != $user->last_project) {
            $user->last_project = $model->project_id;
            $save_user = true;
        }

        if ($model->work_type_id != $user->last_work_type) {
            $user->last_work_type = $model->work_type_id;
            $save_user = true;
        }

        if ($model->location_id != $user->last_location) {
            $user->last_location = $model->location_id;
            $save_user = true;
        }

        if ($save_user) {
            $user->save();
        }

        TimeEntry::setEventDispatcher($dispatcher);
    }

    public function updating($model)
    {
        $dispatcher = TimeEntry::getEventDispatcher();
        TimeEntry::unsetEventDispatcher();

        if ($model->is_timer == 0 && $model->end_time != null) {
            if ($model->pause_time != null) {
                $model->end_time = $model->pause_time;
            } else if ($model->resume_time != null) {
                $model->time_worked += $model->end_time->diffInSeconds($model->resume_time);
            } else if ($model->end_time != null) {
                $model->time_worked = $model->end_time->diffInSeconds($model->start_time);
            }

            $model->pause_time = null;
            $model->resume_time = null;
            $model->save();
        } else if ($model->is_timer == 1 && $model->end_time == null && $model->pause_time != null) {
            $model->time_worked += $model->pause_time->diffInSeconds($model->resume_time ?? $model->start_time);
            $model->save();
        }

        TimeEntry::setEventDispatcher($dispatcher);
    }
}
