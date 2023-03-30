<?php

namespace App;

use App\TimeEntry;
use App\User;

use Illuminate\Database\Eloquent\Model;

class UserHourlyRate extends Model
{
    public $timestamps = false;
    protected $fillable = ['rate', 'created_at'];

    public function time_entries()
    {
        $time_entries = $this->hasMany(TimeEntry::class, 'hourly_rate_id');

        if (!Auth::user()->can('show.time_entries.others')) {
            $time_entries = $time_entries->where('user_id', Auth::user()->id);
        }

        return $time_entries;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
