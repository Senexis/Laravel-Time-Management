<?php

namespace App\Http\Composers;

use App\TimeEntry;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class HeaderComposer
{
    private $user;
    private $send_feedback;
    private $stop_time_entries_self;
    private $running_timer;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        if (!$this->user) {
            if (!Auth::check()) return;
            $this->user = Auth::user();
        }

        if (!$this->send_feedback) {
            $this->send_feedback = $this->user->can('send.feedback');
        }

        if (!$this->stop_time_entries_self) {
            $this->stop_time_entries_self = $this->user->can('stop.time_entries.self');
        }

        if (!$this->running_timer) {
            $this->running_timer = TimeEntry::where('user_id', $this->user->id)
                ->where('is_timer', 1)
                ->first();
        }
    }

    public function compose(View $view)
    {
        $view->with('send_feedback', $this->send_feedback);
        $view->with('stop_time_entries_self', $this->stop_time_entries_self);
        $view->with('running_timer', $this->running_timer ?? false);
    }
}
