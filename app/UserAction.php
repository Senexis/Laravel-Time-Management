<?php

namespace App;

use App\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserAction extends Model
{
    protected $fillable = ['action', 'action_model', 'action_id', 'user_id'];

    public function setUserIdAttribute($input)
    {
        $this->attributes['user_id'] = $input ? $input : null;
    }

    public function getCreatedAtAttribute($input)
    {
        if ($input != null) {
            $time = Carbon::createFromFormat('Y-m-d H:i:s', $input, 'UTC');
            $time->setTimezone(Auth::user()->timezone);

            return $time;
        } else {
            return '';
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
