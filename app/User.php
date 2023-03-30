<?php

namespace App;

use App\TimeEntry;
use App\UserHourlyRate;
use App\UserLocation;

use App\Notifications\ResetPassword as ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasLocalePreference
{
    use HasRoles, SoftDeletes, Notifiable;

    protected $fillable = [
        'is_active', 'name', 'email', 'password', 'api_token', 'hourly_rate', 'travel_expenses', 'timezone', 'locale', 'remember_token', 'timeular_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dirty_hourly_rate;

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            // Fix storing a user not updating the hourly rate.
            if (!empty($model->dirty_hourly_rate)) {
                $model->hourly_rates()->create([
                    'rate' => $model->dirty_hourly_rate,
                    'created_at' => Carbon::createFromTimestamp(0)
                ]);
                $model->dirty_hourly_rate = null;
            }
        });

        User::observe(new \App\Observers\UserActionsObserver);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the user's preferred locale.
     *
     * @return string
     */
    public function preferredLocale()
    {
        return $this->locale;
    }

    public function setPasswordAttribute($input)
    {
        if (!empty($input)) {
            $this->attributes['password'] = Hash::needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function getHourlyRateAttribute()
    {
        $latest_rate = $this->hourly_rates()->latest()->first();

        if (isset($latest_rate)) {
            return number_format($latest_rate->rate, 2);
        } else {
            return number_format(0, 2);
        }
    }

    public function setHourlyRateAttribute($input)
    {
        if ($input == $this->hourly_rate) {
            return;
        }

        if ($input && !empty($this->id)) {
            // Object has been stored before, update directly.
            $this->hourly_rates()->create([
                'rate' => $input,
                'created_at' => Carbon::now()
            ]);
        } else if ($input && empty($this->id)) {
            // We're storing a new object, update through class protected variable.
            $this->dirty_hourly_rate = $input;
        }
    }

    public function getTravelExpensesAttribute($input)
    {
        return number_format($input, 2);
    }

    public function hourly_rates()
    {
        return $this->hasMany(UserHourlyRate::class);
    }

    public function locations()
    {
        return $this->hasMany(UserLocation::class);
    }

    public function time_entries()
    {
        $time_entries = $this->hasMany(TimeEntry::class);

        if (Auth::user() == null) {
            $time_entries = $time_entries->where('user_id', $this->id);
        } else if (!Auth::user()->can('show.time_entries.others')) {
            $time_entries = $time_entries->where('user_id', Auth::user()->id);
        }

        return $time_entries;
    }
}
