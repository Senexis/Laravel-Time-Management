<?php

namespace App\Http\Controllers;

use App\Project;

use App\Http\Requests\StoreProjectsRequest;
use App\Http\Requests\UpdateProjectsRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * Display a listing of Project.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $current_amount = $r->query('amount', null);
        $current_from_time = $r->query('from_time', null);
        $current_to_time = $r->query('to_time', null);
        $current_type = $r->query('type', 'used');

        $projects = Project::with('time_entries.hourly_rate:id,rate')->orderBy('name');
        $amount = 15;

        if (!empty($current_amount) && is_numeric($current_amount)) {
            $amount = intval($current_amount);
        }

        try {
            if (!empty($current_from_time)) {
                $today = Carbon::now();
                $from = Carbon::createFromFormat('Y-m-d H:i:s', $current_from_time);

                $projects = $projects->whereHas('time_entries', function ($query) use ($today, $from) {
                    $query->whereBetween('start_time', [$from, $today]);
                });
            }

            if (!empty($current_to_time)) {
                $to = Carbon::createFromFormat('Y-m-d H:i:s', $current_to_time);

                $projects = $projects->whereHas('time_entries', function ($query) use ($to) {
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
                $projects = $projects->whereHas('time_entries');
            } else if ($current_type == 'unused') {
                $projects = $projects->whereDoesntHave('time_entries');
            }
        }

        if ($amount > 0) {
            $projects = $projects->paginate($amount);
        } else {
            $projects = $projects->get();
        }

        $relations = [
            'wide_container' => true,

            'projects' => $projects,

            'current_amount' => $current_amount,
            'current_from_time' => $current_from_time,
            'current_to_time' => $current_to_time,
            'current_type' => $current_type,

            'create_projects' => Auth::user()->can('create.projects'),
            'delete_projects' => Auth::user()->can('delete.projects'),
            'edit_projects' => Auth::user()->can('edit.projects'),
            'show_money' => Auth::user()->can('show.money'),
            'show_projects' => Auth::user()->can('show.projects'),
        ];

        return view('projects.index', $relations);
    }

    /**
     * Show the form for creating new Project.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created Project in storage.
     *
     * @param  \App\Http\Requests\StoreProjectsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProjectsRequest $request)
    {
        Project::create($request->all());
        return redirect()->route('projects.index');
    }

    /**
     * Show the form for editing Project.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        return view('projects.edit', ['project' => $project]);
    }

    /**
     * Update Project in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectsRequest  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProjectsRequest $request, Project $project)
    {
        $project->update($request->all());
        return redirect()->route('projects.index');
    }

    /**
     * Display Project.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return view('projects.show', ['project' => $project]);
    }

    /**
     * Remove Project from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        // Prevent the user deleting the last item.
        if (Project::whereNull('deleted_at')->count() < 2) {
            abort(403, 'The last project cannot be deleted.');
            return;
        }

        $project->delete();

        return redirect()->route('projects.index');
    }
}
