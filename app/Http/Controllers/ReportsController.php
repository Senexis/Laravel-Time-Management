<?php

namespace App\Http\Controllers;

use App\Project;
use App\TimeEntry;
use App\User;
use App\WorkType;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show.reports.self');
    }

    public function index(Request $r)
    {
        $users_select = User::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id');
        $date_selects = $this->getDateYearSelect();

        $has_timer_items = false;
        $has_unlocked_items = false;
        $has_too_long_items = false;

        $user = Auth::user();

        $show_all = $r->query('all', false);
        $current_week = $r->query('week', null);
        $current_month = $r->query('month', null);
        $current_year = $r->query('year', null);
        $current_user = $r->query('user_id', $user->id);

        if (!$user->can('show.reports.others')) {
            $current_user = $user->id;
        }

        // Set any value that isn't explicitly false to true.
        if ($show_all !== false) {
            $show_all = true;
        }

        $last_entry = null;
        $total_time = 0;
        $total_rate = 0;
        $total_travel_distance = 0;
        $total_entries = 0;

        $entries_daily = [];
        $entries_weekly = [];
        $entries_monthly = [];
        $entries_by_project = [];
        $entries_by_work_type = [];

        $user = User::findOrFail($current_user);

        $time_entries = TimeEntry::with(['project:id,name', 'work_type:id,name', 'location', 'hourly_rate:id,rate', 'user:id,name'])
            ->orderBy('start_time', 'desc')
            ->where('user_id', $user->id);

        if (!$show_all) {
            try {
                if (!empty($current_week)) {
                    $date = CarbonImmutable::create($current_week);
                    $from = $date->startOfWeek();
                    $to = $date->endOfWeek();

                    $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);
                } else if (!empty($current_month) && !empty($current_year)) {
                    $date = CarbonImmutable::createFromFormat('Y n', "$current_year $current_month");
                    $from = $date->startOfMonth();
                    $to = $date->endOfMonth();

                    $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);
                } else if (!empty($current_year)) {
                    $date = CarbonImmutable::createFromFormat('Y', $current_year);
                    $from = $date->startOfYear();
                    $to = $date->endOfYear();

                    $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);
                } else {
                    $date = CarbonImmutable::now();
                    $from = $date->startOfMonth();
                    $to = $date->endOfMonth();

                    $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);

                    $current_month = $date->format('n');
                    $current_year = $date->format('Y');
                }
            } catch (\Throwable $th) {
                $date = CarbonImmutable::now();
                $from = $date->startOfMonth();
                $to = $date->endOfMonth();

                $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);

                $current_month = $date->format('n');
                $current_year = $date->format('Y');
            }
        }

        $time_entries = $time_entries->get();

        foreach ($time_entries as $time_entry) {
            $total_time += $time_entry->time_worked;
            $total_rate += $time_entry->total_wage;
            $total_entries++;

            if (isset($time_entry->location)) {
                $total_travel_distance = $this->getTravelDistanceBetweenEntries($total_travel_distance, $time_entry, $last_entry);
            }

            $entries_daily = $this->addToDailyArray($entries_daily, $time_entry->start_time, $time_entry);
            $entries_weekly = $this->addToWeeklyArray($entries_weekly, $time_entry->start_time, $time_entry);
            $entries_monthly = $this->addToMonthlyArray($entries_monthly, $time_entry->start_time, $time_entry);

            if (isset($time_entry->project)) {
                $entries_by_project = $this->addToArrayWithName($entries_by_project, $time_entry->project->id, $time_entry, $time_entry->project->name);
            }

            if (isset($time_entry->work_type)) {
                $entries_by_work_type = $this->addToArrayWithName($entries_by_work_type, $time_entry->work_type->id, $time_entry, $time_entry->work_type->name);
            }

            if (!$has_timer_items && $time_entry->is_timer) {
                $has_timer_items = true;
            }

            if (!$has_unlocked_items && $time_entry->locked_at == null) {
                $has_unlocked_items = true;
            }

            if (!$has_too_long_items && $time_entry->time_worked > 86400) {
                $has_too_long_items = true;
            }

            $last_entry = $time_entry;
        }

        usort($entries_by_project, function ($a, $b) {
            return $b['time_worked'] <=> $a['time_worked'];
        });

        usort($entries_by_work_type, function ($a, $b) {
            return $b['time_worked'] <=> $a['time_worked'];
        });

        $total_travel_expense = $user->travel_expenses * $total_travel_distance;
        $total_wage = $total_rate + $total_travel_expense;

        $now = CarbonImmutable::now();
        $last_month = new CarbonImmutable('first day of previous month');
        $last_week = new CarbonImmutable('previous week');
        $active_item = 0;

        if (!empty($current_month) && !empty($current_year)) {
            if ($current_month == $now->format('n') && $current_year == $now->format('Y')) {
                $active_item = 1;
            } else if ($current_month == $last_month->format('n') && $current_year == $last_month->format('Y')) {
                $active_item = 2;
            }
        } else if (!empty($current_week)) {
            if ($current_week == $now->format('o\WW')) {
                $active_item = 3;
            } else if ($current_week == $last_week->format('o\WW')) {
                $active_item = 4;
            }
        } else if ($show_all) {
            $active_item = 5;
        }

        $relations = [
            'now' => $now,
            'last_month' => $last_month,
            'last_week' => $last_week,

            'users_select' => $users_select,
            'date_selects' => $date_selects,
            'active_item' => $active_item,

            'current_week' => $current_week,
            'current_month' => $current_month,
            'current_year' => $current_year,
            'current_user' => $current_user,

            'has_timer_items' => $has_timer_items,
            'has_unlocked_items' => $has_unlocked_items,
            'has_too_long_items' => $has_too_long_items,

            'total_time' => $total_time,
            'total_rate' => $total_rate,
            'total_travel_distance' => $total_travel_distance,
            'total_travel_expense' => $total_travel_expense,
            'total_wage' => $total_wage,
            'total_entries' => $total_entries,

            'entries_daily' => $entries_daily,
            'entries_weekly' => $entries_weekly,
            'entries_monthly' => $entries_monthly,
            'entries_by_project' => $entries_by_project,
            'entries_by_work_type' => $entries_by_work_type,
        ];

        return view('reports.index', $relations);
    }

    public function project(Request $r)
    {
        $projects_select = Project::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id')->prepend(__('app.global_select_prepend'), '');
        $date_selects = $this->getDateYearSelect();

        $has_timer_items = false;
        $has_unlocked_items = false;
        $has_too_long_items = false;

        $show_all = $r->query('all', false);
        $current_week = $r->query('week', null);
        $current_month = $r->query('month', null);
        $current_year = $r->query('year', null);
        $current_project = $r->query('project_id', null);

        // Set any value that isn't explicitly false to true.
        if ($show_all !== false) {
            $show_all = true;
        }

        $total_time = 0;
        $total_rate = 0;
        $total_entries = 0;

        $entries_daily = [];
        $entries_weekly = [];
        $entries_monthly = [];
        $entries_by_work_type = [];
        $entries_by_user = [];

        if ($current_project != null) {
            $user = Auth::user();
            $project = Project::with('time_entries.hourly_rate:id,rate')->findOrFail($current_project);
            $time_entries = TimeEntry::with(['project:id,name', 'work_type:id,name', 'hourly_rate:id,rate', 'user:id,name'])
                ->orderBy('start_time', 'desc')
                ->where('project_id', $project->id);

            if (!$user->can('show.reports.others')) {
                $time_entries = $time_entries->where('user_id', $user->id);
            }

            if (!$show_all) {
                try {
                    if (!empty($current_week)) {
                        $date = CarbonImmutable::create($current_week);
                        $from = $date->startOfWeek();
                        $to = $date->endOfWeek();

                        $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);
                    } else if (!empty($current_month) && !empty($current_year)) {
                        $date = CarbonImmutable::createFromFormat('Y n', "$current_year $current_month");
                        $from = $date->startOfMonth();
                        $to = $date->endOfMonth();

                        $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);
                    } else if (!empty($current_year)) {
                        $date = CarbonImmutable::createFromFormat('Y', $current_year);
                        $from = $date->startOfYear();
                        $to = $date->endOfYear();

                        $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);
                    } else {
                        $date = CarbonImmutable::now();
                        $from = $date->startOfMonth();
                        $to = $date->endOfMonth();

                        $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);

                        $current_month = $date->format('n');
                        $current_year = $date->format('Y');
                    }
                } catch (\Throwable $th) {
                    $date = CarbonImmutable::now();
                    $from = $date->startOfMonth();
                    $to = $date->endOfMonth();

                    $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);

                    $current_month = $date->format('n');
                    $current_year = $date->format('Y');
                }
            }

            $time_entries = $time_entries->get();

            foreach ($time_entries as $time_entry) {
                $total_time += $time_entry->time_worked;
                $total_rate += $time_entry->total_wage;
                $total_entries++;

                $entries_daily = $this->addToDailyArray($entries_daily, $time_entry->start_time, $time_entry);
                $entries_weekly = $this->addToWeeklyArray($entries_weekly, $time_entry->start_time, $time_entry);
                $entries_monthly = $this->addToMonthlyArray($entries_monthly, $time_entry->start_time, $time_entry);

                if (isset($time_entry->work_type)) {
                    $entries_by_work_type = $this->addToArrayWithName($entries_by_work_type, $time_entry->work_type->id, $time_entry, $time_entry->work_type->name);
                }

                if (isset($time_entry->user)) {
                    $entries_by_user = $this->addToArrayWithName($entries_by_user, $time_entry->user->id, $time_entry, $time_entry->user->name);
                }

                if (!$has_timer_items && $time_entry->is_timer) {
                    $has_timer_items = true;
                }

                if (!$has_unlocked_items && $time_entry->locked_at == null) {
                    $has_unlocked_items = true;
                }

                if (!$has_too_long_items && $time_entry->time_worked > 86400) {
                    $has_too_long_items = true;
                }
            }

            usort($entries_by_work_type, function ($a, $b) {
                return $b['time_worked'] <=> $a['time_worked'];
            });

            usort($entries_by_user, function ($a, $b) {
                return $b['time_worked'] <=> $a['time_worked'];
            });
        }

        $now = CarbonImmutable::now();
        $last_month = new CarbonImmutable('first day of previous month');
        $last_week = new CarbonImmutable('previous week');
        $active_item = 0;

        if (!empty($current_month) && !empty($current_year)) {
            if ($current_month == $now->format('n') && $current_year == $now->format('Y')) {
                $active_item = 1;
            } else if ($current_month == $last_month->format('n') && $current_year == $last_month->format('Y')) {
                $active_item = 2;
            }
        } else if (!empty($current_week)) {
            if ($current_week == $now->format('o\WW')) {
                $active_item = 3;
            } else if ($current_week == $last_week->format('o\WW')) {
                $active_item = 4;
            }
        } else if ($show_all) {
            $active_item = 5;
        }

        $relations = [
            'now' => $now,
            'last_month' => $last_month,
            'last_week' => $last_week,

            'projects_select' => $projects_select,
            'date_selects' => $date_selects,
            'active_item' => $active_item,

            'current_week' => $current_week,
            'current_month' => $current_month,
            'current_year' => $current_year,
            'current_project' => $current_project,

            'has_timer_items' => $has_timer_items,
            'has_unlocked_items' => $has_unlocked_items,
            'has_too_long_items' => $has_too_long_items,

            'total_time' => $total_time,
            'total_rate' => $total_rate,
            'total_entries' => $total_entries,

            'entries_daily' => $entries_daily,
            'entries_weekly' => $entries_weekly,
            'entries_monthly' => $entries_monthly,
            'entries_by_work_type' => $entries_by_work_type,
            'entries_by_user' => $entries_by_user
        ];

        return view('reports.project', $relations);
    }

    public function workType(Request $r)
    {
        $work_types_select = WorkType::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id')->prepend(__('app.global_select_prepend'), '');
        $date_selects = $this->getDateYearSelect();

        $has_timer_items = false;
        $has_unlocked_items = false;
        $has_too_long_items = false;

        $show_all = $r->query('all', false);
        $current_week = $r->query('week', null);
        $current_month = $r->query('month', null);
        $current_year = $r->query('year', null);
        $current_work_type = $r->query('work_type_id', null);

        // Set any value that isn't explicitly false to true.
        if ($show_all !== false) {
            $show_all = true;
        }

        $total_time = 0;
        $total_rate = 0;
        $total_entries = 0;

        $entries_daily = [];
        $entries_weekly = [];
        $entries_monthly = [];
        $entries_by_project = [];
        $entries_by_user = [];

        if ($current_work_type != null) {
            $user = Auth::user();
            $work_type = WorkType::with('time_entries.hourly_rate:id,rate')->findOrFail($current_work_type);
            $time_entries = TimeEntry::with(['project:id,name', 'work_type:id,name', 'hourly_rate:id,rate', 'user:id,name'])
                ->orderBy('start_time', 'desc')
                ->where('work_type_id', $work_type->id);

            if (!$user->can('show.reports.others')) {
                $time_entries = $time_entries->where('user_id', $user->id);
            }

            if (!$show_all) {
                try {
                    if (!empty($current_week)) {
                        $date = CarbonImmutable::create($current_week);
                        $from = $date->startOfWeek();
                        $to = $date->endOfWeek();

                        $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);
                    } else if (!empty($current_month) && !empty($current_year)) {
                        $date = CarbonImmutable::createFromFormat('Y n', "$current_year $current_month");
                        $from = $date->startOfMonth();
                        $to = $date->endOfMonth();

                        $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);
                    } else if (!empty($current_year)) {
                        $date = CarbonImmutable::createFromFormat('Y', $current_year);
                        $from = $date->startOfYear();
                        $to = $date->endOfYear();

                        $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);
                    } else {
                        $date = CarbonImmutable::now();
                        $from = $date->startOfMonth();
                        $to = $date->endOfMonth();

                        $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);

                        $current_month = $date->format('n');
                        $current_year = $date->format('Y');
                    }
                } catch (\Throwable $th) {
                    $date = CarbonImmutable::now();
                    $from = $date->startOfMonth();
                    $to = $date->endOfMonth();

                    $time_entries = $time_entries->whereBetween('start_time', [$from, $to]);

                    $current_month = $date->format('n');
                    $current_year = $date->format('Y');
                }
            }

            $time_entries = $time_entries->get();

            foreach ($time_entries as $time_entry) {
                $total_time += $time_entry->time_worked;
                $total_rate += $time_entry->total_wage;
                $total_entries++;

                $entries_daily = $this->addToDailyArray($entries_daily, $time_entry->start_time, $time_entry);
                $entries_weekly = $this->addToWeeklyArray($entries_weekly, $time_entry->start_time, $time_entry);
                $entries_monthly = $this->addToMonthlyArray($entries_monthly, $time_entry->start_time, $time_entry);

                if (isset($time_entry->project)) {
                    $entries_by_project = $this->addToArrayWithName($entries_by_project, $time_entry->project->id, $time_entry, $time_entry->project->name);
                }

                if (isset($time_entry->user)) {
                    $entries_by_user = $this->addToArrayWithName($entries_by_user, $time_entry->user->id, $time_entry, $time_entry->user->name);
                }

                if (!$has_timer_items && $time_entry->is_timer) {
                    $has_timer_items = true;
                }

                if (!$has_unlocked_items && $time_entry->locked_at == null) {
                    $has_unlocked_items = true;
                }

                if (!$has_too_long_items && $time_entry->time_worked > 86400) {
                    $has_too_long_items = true;
                }
            }

            usort($entries_by_project, function ($a, $b) {
                return $b['time_worked'] <=> $a['time_worked'];
            });

            usort($entries_by_user, function ($a, $b) {
                return $b['time_worked'] <=> $a['time_worked'];
            });
        }

        $now = CarbonImmutable::now();
        $last_month = new CarbonImmutable('first day of previous month');
        $last_week = new CarbonImmutable('previous week');
        $active_item = 0;

        if (!empty($current_month) && !empty($current_year)) {
            if ($current_month == $now->format('n') && $current_year == $now->format('Y')) {
                $active_item = 1;
            } else if ($current_month == $last_month->format('n') && $current_year == $last_month->format('Y')) {
                $active_item = 2;
            }
        } else if (!empty($current_week)) {
            if ($current_week == $now->format('o\WW')) {
                $active_item = 3;
            } else if ($current_week == $last_week->format('o\WW')) {
                $active_item = 4;
            }
        } else if ($show_all) {
            $active_item = 5;
        }

        $relations = [
            'now' => $now,
            'last_month' => $last_month,
            'last_week' => $last_week,

            'work_types_select' => $work_types_select,
            'date_selects' => $date_selects,
            'active_item' => $active_item,

            'current_week' => $current_week,
            'current_month' => $current_month,
            'current_year' => $current_year,
            'current_work_type' => $current_work_type,

            'has_timer_items' => $has_timer_items,
            'has_unlocked_items' => $has_unlocked_items,
            'has_too_long_items' => $has_too_long_items,

            'total_time' => $total_time,
            'total_rate' => $total_rate,
            'total_entries' => $total_entries,

            'entries_daily' => $entries_daily,
            'entries_weekly' => $entries_weekly,
            'entries_monthly' => $entries_monthly,
            'entries_by_project' => $entries_by_project,
            'entries_by_user' => $entries_by_user
        ];

        return view('reports.work-type', $relations);
    }

    private function getDateYearSelect()
    {
        $first_time_entry = TimeEntry::where('is_timer', 0)
            ->orderBy('start_time')
            ->select('start_time')
            ->first();

        $last_time_entry = TimeEntry::where('is_timer', 0)
            ->orderBy('end_time', 'desc')
            ->select('end_time')
            ->first();

        $weeks = [];
        $months = [];
        $years = [];

        foreach (CarbonPeriod::create($first_time_entry->start_time, '1 week', $last_time_entry->end_time) as $week) {
            $weeks[$week->format('o\WW')] = $week->format(__('app.global_dateweek_format'));
        }

        foreach (CarbonPeriod::create($first_time_entry->start_time, '1 month', $last_time_entry->end_time) as $month) {
            $months[$month->format('n')] = $month->format('F');
        }

        foreach (CarbonPeriod::create($first_time_entry->start_time, '1 year', $last_time_entry->end_time) as $year) {
            $years[$year->format('o')] = $year->format('o');
        }

        $now = CarbonImmutable::now();
        $weeks[$now->format('o\WW')] = $now->format(__('app.global_dateweek_format'));
        $months[$now->format('n')] = $now->format('F');
        $years[$now->format('o')] = $now->format('o');

        ksort($weeks);
        ksort($months);
        ksort($years);

        return ['week' => $weeks, 'month' => $months, 'year' => $years];
    }

    private function getTravelDistanceBetweenEntries($total, $entry, $last_entry = null)
    {
        if ($last_entry != null) {
            $start_time = Carbon::parse($entry->start_time);
            $last_end_time = Carbon::parse($last_entry->end_time);

            // Are we on the same date?
            if ($start_time->diffInDays($last_end_time) == 0) {
                // We are, have we been at the same place earlier?
                if ($entry->location->id != $last_entry->location->id) {
                    // No, we've traveled to a different place.
                    $total += ($entry->location->distance * 2);
                }

                // At this point, we've been at the same place earlier.
                // To prevent duplicate expenses, don't do anything.
            } else {
                // We are not, so this is the first time we've traveled today.
                $total += ($entry->location->distance * 2);
            }
        } else {
            // Add the very first travel distance.
            $total += ($entry->location->distance * 2);
        }

        return $total;
    }

    private function addToArrayWithName($array, $key, $entry, $name)
    {
        if (!isset($array[$key])) {
            $array[$key] = [
                'name' => $name,
                'has_unlocked' => false,
                'has_timer' => false,
                'time_worked' => 0,
                'total_wage' => 0,
            ];
        }

        $array[$key]['time_worked'] += $entry->time_worked;
        $array[$key]['total_wage'] += $entry->total_wage;

        if ($entry->locked_at == null) {
            $array[$key]['has_unlocked'] = true;
        }

        if ($entry->is_timer) {
            $array[$key]['has_timer'] = true;
        }

        return $array;
    }

    private function addToDailyArray($array, $date, $entry)
    {
        $date_key = $date->format(__('app.global_date_format'));

        if (!isset($array[$date_key])) {
            $array[$date_key] = [
                'has_unlocked' => false,
                'has_timer' => false,
                'time_worked' => 0,
                'total_wage' => 0,
            ];
        }

        $array[$date_key]['time_worked'] += $entry->time_worked;
        $array[$date_key]['total_wage'] += $entry->total_wage;

        if ($entry->locked_at == null) {
            $array[$date_key]['has_unlocked'] = true;
        }

        if ($entry->is_timer) {
            $array[$date_key]['has_timer'] = true;
        }

        return $array;
    }

    private function addToWeeklyArray($array, $date, $entry)
    {
        $week_key = $date->format(__('app.global_dateweek_format'));
        $date_key = $date->format(__('app.global_date_format'));

        if (!isset($array[$week_key])) {
            $array[$week_key] = [
                'has_unlocked' => false,
                'has_timer' => false,
                'time_worked' => 0,
                'total_wage' => 0,
            ];
        }

        if (!isset($array[$week_key][$date_key])) {
            $array[$week_key][$date_key] = [
                'has_unlocked' => false,
                'has_timer' => false,
                'time_worked' => 0,
                'total_wage' => 0,
            ];
        }

        $array[$week_key]['time_worked'] += $entry->time_worked;
        $array[$week_key]['total_wage'] += $entry->total_wage;
        $array[$week_key][$date_key]['time_worked'] += $entry->time_worked;
        $array[$week_key][$date_key]['total_wage'] += $entry->total_wage;

        if ($entry->locked_at == null) {
            $array[$week_key]['has_unlocked'] = true;
            $array[$week_key][$date_key]['has_unlocked'] = true;
        }

        if ($entry->is_timer) {
            $array[$week_key]['has_timer'] = true;
            $array[$week_key][$date_key]['has_timer'] = true;
        }

        return $array;
    }

    private function addToMonthlyArray($array, $date, $entry)
    {
        $month_key = $date->format(__('app.global_datemonth_format'));
        $date_key = $date->format(__('app.global_date_format'));

        if (!isset($array[$month_key])) {
            $array[$month_key] = [
                'has_unlocked' => false,
                'has_timer' => false,
                'time_worked' => 0,
                'total_wage' => 0,
            ];
        }

        if (!isset($array[$month_key][$date_key])) {
            $array[$month_key][$date_key] = [
                'has_unlocked' => false,
                'has_timer' => false,
                'time_worked' => 0,
                'total_wage' => 0,
            ];
        }

        $array[$month_key]['time_worked'] += $entry->time_worked;
        $array[$month_key]['total_wage'] += $entry->total_wage;
        $array[$month_key][$date_key]['time_worked'] += $entry->time_worked;
        $array[$month_key][$date_key]['total_wage'] += $entry->total_wage;

        if ($entry->locked_at == null) {
            $array[$month_key]['has_unlocked'] = true;
            $array[$month_key][$date_key]['has_unlocked'] = true;
        }

        if ($entry->is_timer) {
            $array[$month_key]['has_timer'] = true;
            $array[$month_key][$date_key]['has_timer'] = true;
        }

        return $array;
    }
}
