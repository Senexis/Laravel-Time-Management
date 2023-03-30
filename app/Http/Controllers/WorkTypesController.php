<?php

namespace App\Http\Controllers;

use App\WorkType;

use Carbon\Carbon;
use App\Http\Requests\StoreWorkTypesRequest;
use App\Http\Requests\UpdateWorkTypesRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkTypesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(WorkType::class, 'work_type');
    }

    /**
     * Display a listing of WorkType.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $current_amount = $r->query('amount', null);
        $current_from_time = $r->query('from_time', null);
        $current_to_time = $r->query('to_time', null);
        $current_type = $r->query('type', 'used');
        $current_role = $r->query('role_id', null);

        $work_types = WorkType::with('time_entries.hourly_rate:id,rate', 'role:id,name')->orderBy('name');
        $amount = 15;

        if (!empty($current_amount) && is_numeric($current_amount)) {
            $amount = intval($current_amount);
        }

        $show_work_types_others = Auth::user()->can('show.work_types.others');

        try {
            if (!empty($current_from_time)) {
                $today = Carbon::now();
                $from = Carbon::createFromFormat('Y-m-d H:i:s', $current_from_time);

                $work_types = $work_types->whereHas('time_entries', function ($query) use ($today, $from) {
                    $query->whereBetween('start_time', [$from, $today]);
                });
            }

            if (!empty($current_to_time)) {
                $to = Carbon::createFromFormat('Y-m-d H:i:s', $current_to_time);

                $work_types = $work_types->whereHas('time_entries', function ($query) use ($to) {
                    $query->whereBetween('start_time', [0, $to]);
                });
            }
        } catch (\Throwable $th) {
            // One does not simply trust user input.
            $current_from_time = null;
            $current_to_time = null;
        }

        if (!empty($current_type)) {
            if ($current_type == 'used') {
                $work_types = $work_types->whereHas('time_entries');
            } else if ($current_type == 'unused') {
                $work_types = $work_types->whereDoesntHave('time_entries');
            }
        }

        if ($show_work_types_others && !empty($current_role) && $current_role > 0) {
            $work_types = $work_types->where('role_id', $current_role);
        } else if ($show_work_types_others && empty($current_role)) {
            // Go right through.
        } else {
            $user = Auth::user();
            $role_id = $user->roles->first()->id;

            $work_types = $work_types->where(
                function ($query) use ($role_id) {
                    $query
                        ->where('role_id', $role_id)
                        ->orWhereNull('role_id');
                }
            );
        }

        if ($amount > 0) {
            $work_types = $work_types->paginate($amount);
        } else {
            $work_types = $work_types->get();
        }

        $roles_select = Role::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id');

        $relations = [
            'wide_container' => true,

            'work_types' => $work_types,

            'current_amount' => $current_amount,
            'current_from_time' => $current_from_time,
            'current_to_time' => $current_to_time,
            'current_type' => $current_type,
            'current_role' => $current_role,

            'roles_select' => $roles_select,

            'create_work_types' => Auth::user()->can('create.work_types'),
            'delete_work_types_self' => Auth::user()->can('delete.work_types.self'),
            'edit_work_types_self' => Auth::user()->can('edit.work_types.self'),
            'show_money' => Auth::user()->can('show.money'),
            'show_work_types_others' => $show_work_types_others,
            'show_work_types_self' => Auth::user()->can('show.work_types.self'),
        ];

        return view('work_types.index', $relations);
    }

    /**
     * Show the form for creating new WorkType.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id')->prepend(__('app.worktypes_create_role_clear'), '');

        return view('work_types.create', ['roles' => $roles]);
    }

    /**
     * Store a newly created WorkType in storage.
     *
     * @param  \App\Http\Requests\StoreWorkTypesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWorkTypesRequest $request)
    {
        WorkType::create($request->all());

        return redirect()->route('work-types.index');
    }

    /**
     * Show the form for editing WorkType.
     *
     * @param  \App\WorkType  $work_type
     * @return \Illuminate\Http\Response
     */
    public function edit(WorkType $work_type)
    {
        $relations = [
            'roles' => Role::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id')->prepend(__('app.worktypes_edit_role_clear'), ''),
            'work_type' => $work_type,
        ];

        return view('work_types.edit', $relations);
    }

    /**
     * Update WorkType in storage.
     *
     * @param  \App\Http\Requests\UpdateWorkTypesRequest  $request
     * @param  \App\WorkType  $work_type
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWorkTypesRequest $request, WorkType $work_type)
    {
        $work_type->update($request->all());

        return redirect()->route('work-types.index');
    }

    /**
     * Display WorkType.
     *
     * @param  \App\WorkType  $work_type
     * @return \Illuminate\Http\Response
     */
    public function show(WorkType $work_type)
    {
        return view('work_types.show', ['work_type' => $work_type]);
    }

    /**
     * Remove WorkType from storage.
     *
     * @param  \App\WorkType  $work_type
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkType $work_type)
    {
        $work_type->delete();

        return redirect()->route('work-types.index');
    }
}
