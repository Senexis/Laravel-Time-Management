<?php

namespace App\Console\Commands;

use App\TimeEntry;
use App\User;
use App\Notifications\TimeEntriesLockReminder as ReminderNotification;

use Carbon;
use Illuminate\Console\Command;

class TimeEntriesLockReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:lock-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email to users with unlocked time entries since the first day of the last month';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $counter = 0;
        $users = User::get();
        $last_month = Carbon::now()->startOfMonth()->subMonth();

        foreach ($users as $user) {
            $has_unlocked = TimeEntry::whereNull('locked_at')
                ->where('user_id', $user->id)
                ->whereMonth('created_at', $last_month->month)
                ->whereYear('created_at', $last_month->year)
                ->exists();

            if (!$has_unlocked) continue;

            try {
                $user->notify(new ReminderNotification());
                $counter++;
            } catch (\Throwable $th) {
                $this->error('An e-mail failed to send: ' . $th->getMessage());
            }
        }

        $this->info($counter . ' reminder email(s) have been sent.');
    }
}
