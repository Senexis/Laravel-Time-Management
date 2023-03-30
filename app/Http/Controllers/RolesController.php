<?php

namespace App\Http\Controllers;

use App\User;

use App\Http\Requests\StoreRolesRequest;
use App\Http\Requests\UpdateRolesRequest;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of Role.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::orderBy('name')->paginate(12);

        foreach ($roles as $role) {
            $users = User::role($role->name)->select('id', 'name', 'email')->limit(4)->get();

            $role->users = $users;
            $role->user_count = $users->count();
        }

        $relations = [
            'roles' => $roles,

            'create_roles' => Auth::user()->can('create.roles'),
            'delete_roles' => Auth::user()->can('delete.roles'),
            'edit_roles' => Auth::user()->can('edit.roles'),
            'show_roles' => Auth::user()->can('show.roles'),
        ];

        return view('roles.index', $relations);
    }

    /**
     * Show the form for creating new Role.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get()->pluck('name');
        $permissions_categorized = $this->getCategorizedPermissions($permissions);

        return view('roles.create', ['permissions' => $permissions_categorized]);
    }

    /**
     * Store a newly created Role in storage.
     *
     * @param  \App\Http\Requests\StoreRolesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRolesRequest $request)
    {
        $role = Role::create(['name' => $request->input('name')]);

        // Sync permissions
        $permissions = [];
        foreach (Permission::all()->pluck('name') as $key) {
            $permission = 'permissions_' . str_replace('.', '_', $key);

            if ($request->input($permission) !== null) {
                array_push($permissions, $key);
            }
        }

        $role->syncPermissions($permissions);

        return redirect()->route('roles.index');
    }

    /**
     * Show the form for editing Role.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->pluck('name');
        $permissions_categorized = $this->getCategorizedPermissions($permissions);

        $relations = [
            'permissions_owned' => $role->permissions->pluck('name')->toArray(),
            'permissions' => $permissions_categorized,
            'role' => $role,
        ];

        return view('roles.edit', $relations);
    }

    /**
     * Update Role in storage.
     *
     * @param  \App\Http\Requests\UpdateRolesRequest  $request
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRolesRequest $request, Role $role)
    {
        $role->update(['name' => $request->input('name')]);

        if ($role->id != 1) {
            // Sync permissions
            $permissions = [];
            foreach (Permission::all()->pluck('name') as $key) {
                $permission = 'permissions_' . str_replace('.', '_', $key);

                if ($request->input($permission) !== null) {
                    array_push($permissions, $key);
                }
            }

            $role->syncPermissions($permissions);
        } else {
            // Always ensure the admin role has ALL permissions.
            $role->syncPermissions(Permission::all());
        }

        return redirect()->route('roles.index');
    }

    /**
     * Display Role.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->pluck('name');
        $permissions_categorized = $this->getCategorizedPermissions($permissions);

        $relations = [
            'permissions_owned' => $role->permissions->pluck('name')->toArray(),
            'permissions' => $permissions_categorized,
            'role' => $role,
        ];

        return view('roles.show', $relations);
    }

    /**
     * Remove Role from storage.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index');
    }

    private function getCategorizedPermissions($permissions)
    {
        $result = [];
        $uncategorized = [];

        foreach ($permissions as $permission) {
            // Remove permission if it doesn't have a translation.
            if (strpos(__('permissions.' . $permission), 'permissions.') !== false) continue;

            $array = explode('.', $permission);

            if (count($array) == 3) {
                if (!isset($result[$array[2]])) {
                    $result[$array[2]] = [
                        'name' => $array[2],
                        'values' => []
                    ];
                }

                array_push($result[$array[2]]['values'], $permission);
            } else {
                array_push($uncategorized, $permission);
            }
        }

        ksort($result);

        if (count($uncategorized) > 0) {
            // Always add the uncategorized permissions last.
            $result['uncategorized'] = [
                'name' => 'uncategorized',
                'values' => $uncategorized
            ];
        }

        return $result;
    }
}
