<?php

namespace App;

use App\Project;
use App\User;
use App\UserHourlyRate;
use App\UserLocation;
use App\WorkType;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TimeEntry extends Model
{
    use SoftDeletes;

    protected $fillable = ['project_id', 'work_type_id', 'location_id', 'hourly_rate_id', 'start_time', 'end_time', 'time_worked', 'notes', 'is_timer', 'timeular_entry_id'];

    protected $touches = ['project', 'work_type'];

    protected $casts = [
        'start_time' => 'datetime',
        'pause_time' => 'datetime',
        'resume_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        TimeEntry::observe(new \App\Observers\TimeEntryModelObserver);
        TimeEntry::observe(new \App\Observers\UserActionsObserver);
    }

    public function setProjectIdAttribute($input)
    {
        $this->attributes['project_id'] = !empty($input) ? $input : null;
    }

    public function setWorkTypeIdAttribute($input)
    {
        $this->attributes['work_type_id'] = !empty($input) ? $input : null;
    }

    public function setStartTimeAttribute($input)
    {
        if ($input != null) {
            if (Auth::check()) {
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input, Auth::user()->timezone);
                $time->setTimezone('UTC');
            } else {
                // Database seeders use this.
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input);
            }

            $this->attributes['start_time'] = $time;
        } else {
            $this->attributes['start_time'] = null;
        }
    }

    public function getStartTimeAttribute($input)
    {
        if ($input != null) {
            if (Auth::check()) {
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input, 'UTC');
                $time->setTimezone(Auth::user()->timezone);
            } else {
                // Database seeders use this.
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input);
            }

            return $time;
        } else {
            return null;
        }
    }

    public function setPauseTimeAttribute($input)
    {
        if ($input != null) {
            if (Auth::check()) {
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input, Auth::user()->timezone);
                $time->setTimezone('UTC');
            } else {
                // Database seeders use this.
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input);
            }

            $this->attributes['pause_time'] = $time;
        } else {
            $this->attributes['pause_time'] = null;
        }
    }

    public function getPauseTimeAttribute($input)
    {
        if ($input != null) {
            if (Auth::check()) {
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input, 'UTC');
                $time->setTimezone(Auth::user()->timezone);
            } else {
                // Database seeders use this.
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input);
            }

            return $time;
        } else {
            return null;
        }
    }

    public function setResumeTimeAttribute($input)
    {
        if ($input != null) {
            if (Auth::check()) {
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input, Auth::user()->timezone);
                $time->setTimezone('UTC');
            } else {
                // Database seeders use this.
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input);
            }

            $this->attributes['resume_time'] = $time;
        } else {
            $this->attributes['resume_time'] = null;
        }
    }

    public function getResumeTimeAttribute($input)
    {
        if ($input != null) {
            if (Auth::check()) {
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input, 'UTC');
                $time->setTimezone(Auth::user()->timezone);
            } else {
                // Database seeders use this.
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input);
            }

            return $time;
        } else {
            return null;
        }
    }

    public function setEndTimeAttribute($input)
    {
        if ($input != null) {
            if (Auth::check()) {
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input, Auth::user()->timezone);
                $time->setTimezone('UTC');
            } else {
                // Database seeders use this.
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input);
            }

            $this->attributes['end_time'] = $time;
        } else {
            $this->attributes['end_time'] = null;
        }
    }

    public function getEndTimeAttribute($input)
    {
        if ($input != null) {
            if (Auth::check()) {
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input, 'UTC');
                $time->setTimezone(Auth::user()->timezone);
            } else {
                // Database seeders use this.
                $time = Carbon::createFromFormat('Y-m-d H:i:s', $input);
            }

            return $time;
        } else {
            return null;
        }
    }

    public function getIsTimerAttribute($input)
    {
        return $this->end_time == null || $input;
    }

    public function getIsRunningAttribute()
    {
        return $this->is_timer && $this->pause_time == null;
    }

    public function getIsValidAttribute()
    {
        if ($this->total_wage < 0) return false;
        else if ($this->project == null || $this->project->name == null) return false;
        else if ($this->work_type == null || $this->work_type->name == null) return false;
        else if ($this->location == null || $this->location->name == null) return false;
        else return true;
    }

    public function getTimeWorkedAttribute($input)
    {
        if ($this->is_timer && $this->pause_time == null) {
            $begin = Carbon::parse($this->resume_time ?? $this->start_time);
            $end = Carbon::now();
            $seconds = $begin->diffInSeconds($end);

            if ($this->resume_time != null) $seconds += $this->attributes['time_worked'];

            return $seconds;
        }

        return $input;
    }

    public function getTotalWageAttribute()
    {
        $current_rate = null;

        if ($this->hourly_rate == null) {
            $rates = $this->user->hourly_rates->sortByDesc('created_at');

            foreach ($rates as $rate) {
                if ($rate->created_at <= $this->attributes['start_time']) {
                    $current_rate = $rate;
                    break;
                }
            }

            if ($current_rate === null) {
                return -1;
            }

            $dispatcher = TimeEntry::getEventDispatcher();
            TimeEntry::unsetEventDispatcher();

            $this->hourly_rate_id = $current_rate->id;
            $this->save();

            TimeEntry::setEventDispatcher($dispatcher);
        } else {
            $current_rate = $this->hourly_rate;
        }

        $time = $this->attributes['time_worked'];
        return ($current_rate->rate / 3600) * $time;
    }

    public function hourly_rate()
    {
        return $this->belongsTo(UserHourlyRate::class, 'hourly_rate_id');
    }

    public function location()
    {
        return $this->belongsTo(UserLocation::class, 'location_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function work_type()
    {
        return $this->belongsTo(WorkType::class, 'work_type_id');
    }
}
