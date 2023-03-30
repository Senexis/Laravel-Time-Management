<?php

namespace App;

use App\TimeEntry;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class WorkType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'role_id'];

    public static function boot()
    {
        parent::boot();
        WorkType::observe(new \App\Observers\UserActionsObserver);
    }

    public function setRoleIdAttribute($input)
    {
        $this->attributes['role_id'] = !empty($input) ? $input : null;
    }

    public function getTimeWorkedAttribute()
    {
        $result = 0;

        foreach ($this->time_entries as $entry) {
            $result += $entry->time_worked;
        }

        return $result;
    }

    public function getTotalWageAttribute()
    {
        $result = 0;

        foreach ($this->time_entries as $entry) {
            $result += $entry->total_wage;
        }

        return $result;
    }

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }

    public function time_entries()
    {
        $time_entries = $this->hasMany(TimeEntry::class);

        if (!Auth::user()->can('show.time_entries.others')) {
            $time_entries = $time_entries->where('user_id', Auth::user()->id);
        }

        return $time_entries;
    }
}
