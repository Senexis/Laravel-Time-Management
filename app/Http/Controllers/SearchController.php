<?php

namespace App\Http\Controllers;

use App\Project;
use App\TimeEntry;
use App\User;
use App\UserLocation;
use App\WorkType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class SearchController extends Controller
{
    /**
     * Display search results.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $user = Auth::user();
        $role_id = $user->roles->first()->id;

        $current_query = $r->query('q', null);
        $current_query = urldecode($current_query);
        $current_query = strtolower($current_query);

        $locations = [];
        $projects = [];
        $roles = [];
        $time_entries = [];
        $users = [];
        $work_types = [];

        $list_locations_self = $user->can('list.locations.self');
        $list_locations_others = $user->can('list.locations.others');
        $list_projects = $user->can('list.projects');
        $list_roles = $user->can('list.roles');
        $list_time_entries_self = $user->can('list.time_entries.self');
        $list_time_entries_others = $user->can('list.time_entries.others');
        $list_users = $user->can('list.users');
        $list_work_types_self = $user->can('list.work_types.self');
        $list_work_types_others = $user->can('list.work_types.others');

        if (!empty($current_query)) {
            if ($list_locations_self) {
                $locations = UserLocation::with(['user:id,name'])
                    ->where('name', 'LIKE', '%' . $current_query . '%');

                if (!$list_locations_others) {
                    $locations = $locations->where('user_id', $user->id);
                }

                $locations = $locations->orderBy('name');
                $locations = $locations->limit(15);
                $locations = $locations->get();
            }

            if ($list_projects) {
                $projects = Project::with('time_entries.hourly_rate:id,rate')->where('name', 'LIKE', '%' . $current_query . '%');

                $projects = $projects->orderBy('name');
                $projects = $projects->limit(15);
                $projects = $projects->get();
            }

            if ($list_roles) {
                $roles = Role::where('name', 'LIKE', '%' . $current_query . '%');

                $roles = $roles->orderBy('name');
                $roles = $roles->limit(15);
                $roles = $roles->get();

                foreach ($roles as $role) {
                    $role->user_count = User::role($role->name)->count();
                }
            }

            if ($list_time_entries_self) {
                $time_entries = TimeEntry::with(['project:id,name', 'work_type:id,name', 'location:id,name', 'hourly_rate:id,rate', 'user:id,name'])
                    ->where('notes', 'LIKE', '%' . $current_query . '%');

                if (!$list_time_entries_others) {
                    $time_entries = $time_entries->where('user_id', $user->id);
                }

                $time_entries = $time_entries->orderBy('start_time', 'desc');
                $time_entries = $time_entries->limit(15);
                $time_entries = $time_entries->get();
            }

            if ($list_users) {
                $users = User::where('name', 'LIKE', '%' . $current_query . '%');

                $users = $users->orderBy('is_active', 'desc')->orderBy('name');
                $users = $users->limit(15);
                $users = $users->get();
            }

            if ($list_work_types_self) {
                $work_types = WorkType::with(['time_entries.hourly_rate:id,rate', 'role:id,name'])
                    ->where('name', 'LIKE', '%' . $current_query . '%');

                if (!$list_work_types_others) {
                    $work_types = $work_types->where(
                        function ($query) use ($role_id) {
                            $query
                                ->where('role_id', $role_id)
                                ->orWhereNull('role_id');
                        }
                    );
                }

                $work_types = $work_types->orderBy('name');
                $work_types = $work_types->limit(15);
                $work_types = $work_types->get();
            }
        }

        $locations_count = count($locations);
        $projects_count = count($projects);
        $roles_count = count($roles);
        $time_entries_count = count($time_entries);
        $users_count = count($users);
        $work_types_count = count($work_types);

        $current_tab = false;

        if ($locations_count > 0) {
            $current_tab = 1;
        } else if ($projects_count > 0) {
            $current_tab = 2;
        } else if ($roles_count > 0) {
            $current_tab = 3;
        } else if ($time_entries_count > 0) {
            $current_tab = 4;
        } else if ($users_count > 0) {
            $current_tab = 5;
        } else if ($work_types_count > 0) {
            $current_tab = 6;
        }

        $relations = [
            'current_query' => $current_query,
            'current_tab' => $current_tab,

            'locations' => $locations,
            'projects' => $projects,
            'roles' => $roles,
            'time_entries' => $time_entries,
            'users' => $users,
            'work_types' => $work_types,

            'locations_count' => $locations_count,
            'projects_count' => $projects_count,
            'roles_count' => $roles_count,
            'time_entries_count' => $time_entries_count,
            'users_count' => $users_count,
            'work_types_count' => $work_types_count,

            'list_locations_self' => $list_locations_self,
            'list_locations_others' => $list_locations_others,
            'list_projects' => $list_projects,
            'list_roles' => $list_roles,
            'list_time_entries_self' => $list_time_entries_self,
            'list_time_entries_others' => $list_time_entries_others,
            'list_users' => $list_users,
            'list_work_types_self' => $list_work_types_self,
            'list_work_types_others' => $list_work_types_others,

            'show_locations_others' =>          $user->can('show.locations.others'),
            'show_locations_self' =>            $user->can('show.locations.self'),
            'show_money' =>                     $user->can('show.money'),
            'show_projects' =>                  $user->can('show.projects'),
            'show_time_entries_others' =>       $user->can('show.time_entries.others'),
            'show_time_entries_self' =>         $user->can('show.time_entries.self'),
            'show_work_types_others' =>         $user->can('show.work_types.others'),
            'show_work_types_self' =>           $user->can('show.work_types.self'),
        ];

        return view('search.index', $relations);
    }
}
