<?php

namespace App\Notifications;

use Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class TimeEntriesLockReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $last_month;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->last_month = Carbon::now()->startOfMonth()->subMonth()->format('F');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(Lang::get('Time Entries Lock Reminder'))
            ->line(Lang::get(":user, it's time to lock your time entries again.", ['user' => $notifiable->name]))
            ->line(Lang::get('It seems you have unlocked time entries for :month. Locking them lets the administrative people know that you checked your time entries and will give them the OK to process your time entries for :month.', ['month' => $this->last_month]))
            ->line(Lang::get('Please click the bottom below, check your time entries, and lock them once you made sure the data you provided is correct.'))
            ->action(Lang::get('View Time Entries'), route('time-entries.index'));
    }
}
