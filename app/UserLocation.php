<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class UserLocation extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'distance'];

    public static function boot()
    {
        parent::boot();
        UserLocation::observe(new \App\Observers\UserActionsObserver);
    }

    public function getTripCostAttribute() {
        return $this->user->travel_expenses * $this->distance;
    }

    public function time_entries()
    {
        $time_entries = $this->hasMany(TimeEntry::class, 'location_id');

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
