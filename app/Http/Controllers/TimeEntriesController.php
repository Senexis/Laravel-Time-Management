<?php

namespace App\Http\Controllers;

use App\Project;
use App\TimeEntry;
use App\User;
use App\UserLocation;
use App\WorkType;

use Carbon\Carbon;
use App\Http\Requests\StoreTimeEntriesRequest;
use App\Http\Requests\UpdateTimeEntriesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeEntriesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TimeEntry::class, 'time_entry');
    }

    /**
     * Display a listing of TimeEntry.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $time_entries = $this->getTimeEntries($request);

        $projects_select = Project::select('id', 'name')->orderBy('name')->pluck('name', 'id')->prepend(__('app.global_select_prepend'), '');
        $work_types_select = WorkType::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id')->prepend(__('app.global_select_prepend'), '');
        $users_select = User::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id')->prepend(__('app.global_select_prepend'), '');

        $relations = [
            'wide_container' => true,

            'time_entries' => $time_entries,

            'current_amount' => $request->query('amount', null),
            'current_from_time' => $request->query('from_time', null),
            'current_to_time' => $request->query('to_time', null),
            'current_type' => $request->query('type', null),
            'current_project' => $request->query('project_id', null),
            'current_work_type' => $request->query('work_type_id', null),
            'current_user' => $request->query('user_id', null),

            'projects_select' => $projects_select,
            'work_types_select' => $work_types_select,
            'users_select' => $users_select,

            // Permissions included here for performance benefits.
            'create_time_entries' =>            $user->can('create.time_entries'),
            'delete_time_entries_self' =>       $user->can('delete.time_entries.self'),
            'edit_time_entries_self' =>         $user->can('edit.time_entries.self'),
            'lock_time_entries_batch' =>        $user->can('lock.time_entries.batch'),
            'lock_time_entries_self' =>         $user->can('lock.time_entries.self'),
            'show_money' =>                     $user->can('show.money'),
            'show_time_entries_others' =>       $user->can('show.time_entries.others'),
            'show_time_entries_self' =>         $user->can('show.time_entries.self'),
            'stop_time_entries_self' =>         $user->can('stop.time_entries.self'),
            'unlock_time_entries_batch' =>      $user->can('unlock.time_entries.batch'),
            'unlock_time_entries_self' =>       $user->can('unlock.time_entries.self'),
        ];

        return view('time_entries.index', $relations);
    }

    /**
     * Show the form for creating new TimeEntry.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $role_id = $user->roles->first()->id;

        if ($user->can('list.work_types.others')) {
            $work_types = WorkType::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id');
        } else {
            $work_types = WorkType::select('id', 'name')->where('role_id', $role_id)->orWhereNull('role_id')->orderBy('name')->get()->pluck('name', 'id');
        }

        $relations = [
            'locations' => $user->locations->sortBy('name')->pluck('name', 'id'),
            'projects' => Project::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id'),
            'time' => Carbon::now($user->timezone),
            'timers_count' => $user->time_entries->where('is_timer', 1)->count(),
            'user' => $user,
            'work_types' => $work_types,
        ];

        return view('time_entries.create', $relations);
    }

    /**
     * Store a newly created TimeEntry in storage.
     *
     * @param  \App\Http\Requests\StoreTimeEntriesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTimeEntriesRequest $request)
    {
        if (!Auth::user()->can('list.work_types.others')) {
            $user_id = Auth::user()->id;
            $user = User::where('id', $user_id)->first();
            $role_id = $user->roles->first()->id;
            $work_types = WorkType::where('role_id', $role_id)->orWhereNull('role_id')->get()->pluck('id');

            $ownedWorkType = $work_types->contains($request->work_type_id);
            $ownedLocation = UserLocation::where('user_id', $user_id)->find($request->location_id);

            if ($ownedLocation == null || !$ownedWorkType) {
                abort(403, 'Location validation failed; user doesn\'t own location or work type.');
            }
        }

        if ($request->is_timer == 1) {
            $request->merge([
                'start_time' => Carbon::now(Auth::user()->timezone),
                'end_time' => null
            ]);
        }

        Auth::user()->time_entries()->create($request->all());
        return redirect()->route('time-entries.index');
    }

    /**
     * Show the form for editing TimeEntry.
     *
     * @param  \App\TimeEntry  $time_entry
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeEntry $time_entry)
    {
        $user_id = $time_entry->user_id;
        $user = User::where('id', $user_id)->first();
        $role_id = $user->roles->first()->id;

        if ($user->can('list.work_types.others')) {
            $work_types = WorkType::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id');
        } else {
            $work_types = WorkType::select('id', 'name')->where('role_id', $role_id)->orWhereNull('role_id')->orderBy('name')->get()->pluck('name', 'id');
        }

        $relations = [
            'locations' => UserLocation::select('id', 'name')->where('user_id', $time_entry->user_id)->orderBy('name')->get()->pluck('name', 'id'),
            'projects' => Project::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id'),
            'time_entry' => $time_entry,
            'work_types' => $work_types,
        ];

        return view('time_entries.edit', $relations);
    }

    /**
     * Update TimeEntry in storage.
     *
     * @param  \App\Http\Requests\UpdateTimeEntriesRequest  $request
     * @param  \App\TimeEntry  $time_entry
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimeEntriesRequest $request, TimeEntry $time_entry)
    {
        $user_id = $time_entry->user_id;
        $user = User::where('id', $user_id)->first();
        $role_id = $user->roles->first()->id;
        $work_types = WorkType::where('role_id', $role_id)->orWhereNull('role_id')->get()->pluck('id');

        $ownedWorkType = $work_types->contains($request->work_type_id);
        $ownedLocation = UserLocation::where('user_id', $user_id)->find($request->location_id);

        if ($ownedLocation == null || !$ownedWorkType) {
            abort(403, 'Location validation failed; user doesn\'t own location or work type.');
        }

        $time_entry->update($request->except(['is_timer']));

        return redirect()->route('time-entries.index');
    }

    /**
     * Display TimeEntry.
     *
     * @param  \App\TimeEntry  $time_entry
     * @return \Illuminate\Http\Response
     */
    public function show(TimeEntry $time_entry)
    {
        return view('time_entries.show', ['time_entry' => $time_entry]);
    }

    /**
     * Remove TimeEntry from storage.
     *
     * @param  \App\TimeEntry  $time_entry
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimeEntry $time_entry)
    {
        $time_entry->delete();

        return redirect()->route('time-entries.index');
    }

    /**
     * Lock TimeEntry.
     *
     * @param  \App\TimeEntry  $time_entry
     * @return \Illuminate\Http\Response
     */
    public function lock(TimeEntry $time_entry)
    {
        $this->authorize('lock', $time_entry);

        $time_entry->locked_at = Carbon::now()->toDateTimeString();
        $time_entry->save();

        return redirect()->back();
    }

    /**
     * Unlock TimeEntry.
     *
     * @param  \App\TimeEntry  $time_entry
     * @return \Illuminate\Http\Response
     */
    public function unlock(TimeEntry $time_entry)
    {
        $this->authorize('unlock', $time_entry);

        $time_entry->locked_at = null;
        $time_entry->save();

        return redirect()->back();
    }

    /**
     * Mass lock TimeEntry.
     *
     * @return \Illuminate\Http\Response
     */
    public function batchlock(Request $request)
    {
        $this->authorize('batchLock', TimeEntry::class);

        $entries = $request->entries;

        if ($entries == null || preg_match("/[^\d,]/", $entries)) {
            abort(403, 'Malformed details.');
        }

        // Turn the string into an Array.
        $entries = explode(',', $entries);

        // Turn the Array into time entries.
        $time_entries = TimeEntry::whereIn('id', $entries)->get();

        // Lock all the found time entries.
        foreach ($time_entries as $time_entry) {
            $this->authorize('lock', $time_entry);

            $time_entry->locked_at = Carbon::now()->toDateTimeString();
            $time_entry->save();
        }

        return redirect()->back();
    }

    /**
     * Mass lock TimeEntry.
     *
     * @return \Illuminate\Http\Response
     */
    public function batchunlock(Request $request)
    {
        $this->authorize('batchUnlock', TimeEntry::class);

        $entries = $request->entries;

        if ($entries == null || preg_match("/[^\d,]/", $entries)) {
            abort(403, 'Malformed details.');
        }

        // Turn the string into an Array.
        $entries = explode(',', $entries);

        // Turn the Array into time entries.
        $time_entries = TimeEntry::whereIn('id', $entries)->get();

        // Lock all the found time entries.
        foreach ($time_entries as $time_entry) {
            $this->authorize('unlock', $time_entry);

            $time_entry->locked_at = null;
            $time_entry->save();
        }

        return redirect()->back();
    }

    /**
     * Pause a timer TimeEntry.
     *
     * @param  \App\TimeEntry  $time_entry
     * @return \Illuminate\Http\Response
     */
    public function pause(TimeEntry $time_entry)
    {
        $this->authorize('stop', $time_entry);

        if ($time_entry->pause_time != null) {
            return redirect()->back();
        }

        $time_entry->pause_time = Carbon::now(Auth::user()->timezone);
        $time_entry->save();

        return redirect()->back();
    }

    /**
     * Pause a timer TimeEntry.
     *
     * @param  \App\TimeEntry  $time_entry
     * @return \Illuminate\Http\Response
     */
    public function resume(TimeEntry $time_entry)
    {
        $this->authorize('stop', $time_entry);

        if ($time_entry->pause_time == null) {
            return redirect()->back();
        }

        $time_entry->resume_time = Carbon::now(Auth::user()->timezone);
        $time_entry->pause_time = null;
        $time_entry->save();

        return redirect()->back();
    }

    /**
     * Stop a timer TimeEntry.
     *
     * @param  \App\TimeEntry  $time_entry
     * @return \Illuminate\Http\Response
     */
    public function stop(TimeEntry $time_entry)
    {
        $this->authorize('stop', $time_entry);

        $time_entry->end_time = Carbon::now(Auth::user()->timezone);
        $time_entry->is_timer = 0;
        $time_entry->save();

        return redirect()->back();
    }

    /**
     * Export all time entries to CSV using the same filter logic as index.
     *
     * @param  \App\TimeEntry  $time_entry
     * @return \Illuminate\Http\Response
     */
    public function exportCsv(Request $request)
    {
        $this->authorize('viewAny', TimeEntry::class);

        $user = Auth::user();
        $show_money = $user->can('show.money');

        $fileName = 'time_entries.csv';
        $time_entries = $this->getTimeEntries($request);

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'User', 'Project', 'Work Type', 'Location', 'Start Time', 'End Time', 'Total Wage', 'Time Worked', 'Notes', 'Is Timer'];

        $callback = function () use ($time_entries, $columns, $show_money) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($time_entries as $time_entry) {
                fputcsv($file, [
                    'ID'                => $time_entry->id,
                    'User'              => $time_entry->user->name,
                    'Project'           => $time_entry->project->name,
                    'Work Type'         => $time_entry->work_type->name,
                    'Location'          => $time_entry->location->name,
                    'Start Time'        => $time_entry->start_time,
                    'End Time'          => $time_entry->end_time,
                    'Total Wage'        => $show_money ? number_format($time_entry->total_wage, 2) : "N/A",
                    'Time Worked'       => floor($time_entry->time_worked / 60),
                    'Notes'             => $time_entry->notes,
                    'Is Timer'          => $time_entry->is_timer,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getTimeEntries($request)
    {
        $user = Auth::user();
        $show_time_entries_others = $user->can('show.time_entries.others');

        $current_amount = $request->query('amount', null);
        $current_from_time = $request->query('from_time', null);
        $current_to_time = $request->query('to_time', null);
        $current_type = $request->query('type', null);
        $current_project = $request->query('project_id', null);
        $current_work_type = $request->query('work_type_id', null);
        $current_user = $request->query('user_id', null);

        $amount = 15;
        if (!empty($current_amount) && is_numeric($current_amount)) {
            $amount = intval($current_amount);
        }

        $time_entries = TimeEntry::with(['project:id,name', 'work_type:id,name', 'location:id,name', 'hourly_rate:id,rate', 'user:id,name']);

        try {
            if (!empty($current_from_time)) {
                $today = Carbon::now();
                $from = Carbon::createFromFormat('Y-m-d H:i:s', $current_from_time);
                $time_entries = $time_entries->whereBetween('start_time', [$from, $today]);
            }

            if (!empty($current_to_time)) {
                $to = Carbon::createFromFormat('Y-m-d H:i:s', $current_to_time);
                $time_entries = $time_entries->whereBetween('start_time', [0, $to]);
            }
        } catch (\Throwable $th) {
            // One does not simply trust user input.
            $current_from_time = null;
            $current_to_time = null;
        }

        if (!empty($current_type)) {
            if ($current_type == 'unlocked') {
                $time_entries = $time_entries->where('locked_at', null);
            } else if ($current_type == 'locked') {
                $time_entries = $time_entries->where('locked_at', '!=', null);
            } else if ($current_type == 'timer') {
                $time_entries = $time_entries->where('is_timer', '1');
            } else if ($current_type == 'trashed' && $show_time_entries_others) {
                // For debug purposes, allows us to force generate fields for trashed entries.
                $time_entries = $time_entries->withTrashed();
            }
        }

        if (!empty($current_work_type)) {
            $time_entries = $time_entries->where('work_type_id', $current_work_type);
        }

        if (!empty($current_project)) {
            $time_entries = $time_entries->where('project_id', $current_project);
        }

        if ($show_time_entries_others && !empty($current_user)) {
            $time_entries = $time_entries->where('user_id', $current_user);
        } elseif ($show_time_entries_others && empty($current_user)) {
            // Go right through.
        } else {
            $time_entries = $time_entries->where('user_id', $user->id);
        }

        if ($amount > 0) {
            $time_entries = $time_entries->orderBy('start_time', 'desc')->paginate($amount);
        } else {
            $time_entries = $time_entries->orderBy('start_time', 'desc')->get();
        }

        return $time_entries;
    }
}
