<?php

namespace App\Http\Controllers;

use App\User;

use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUsersRequest;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of User.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('is_active', 'desc')->orderBy('name')->paginate(12);

        $relations = [
            'users' => $users,

            'create_users' => Auth::user()->can('create.users'),
            'delete_users' => Auth::user()->can('delete.users'),
            'edit_users' => Auth::user()->can('edit.users'),
            'login_as_users' => Auth::user()->can('login_as.users'),
            'show_users' => Auth::user()->can('show.users'),
        ];

        return view('users.index', $relations);
    }

    /**
     * Show the form for creating new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);

        $relations = [
            'locales'       => config('locales'),
            'roles'         => Role::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id')->prepend(__('app.global_select_prepend'), ''),
            'timezones'     => array_combine($timezones, $timezones),
        ];

        return view('users.create', $relations);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\StoreUsersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUsersRequest $request)
    {
        $user = User::create($request->all());
        $user->syncRoles($request->role_id);

        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing User.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        $relations = [
            'locales' => config('locales'),
            'role_id' => $user->roles->first()->id,
            'roles' => Role::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id')->prepend(__('app.global_select_prepend'), ''),
            'timezones' => array_combine($timezones, $timezones),
            'user' => $user,
        ];

        return view('users.edit', $relations);
    }

    /**
     * Update User in storage.
     *
     * @param  \App\Http\Requests\UpdateUsersRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUsersRequest $request, User $user)
    {
        $token = Str::random(60);
        $request->request->add(['api_token' => $token]);

        if ($user->id != Auth::user()->id) {
            // Only allow deactivation if edited user isn't the current user.
            $user->is_active = $request->input('is_active') ?? false;
        }

        $user->update($request->except(['is_active']));

        if ($user->id != Auth::user()->id) {
            // Only allow role updates if edited user isn't the current user.
            $user->syncRoles($request->role_id);
        }

        return redirect()->route('users.index');
    }

    /**
     * Display User.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $relations = [
            'roles' => Role::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id')->prepend(__('app.global_select_prepend'), ''),
            'user' => $user,
        ];

        return view('users.show', $relations);
    }

    /**
     * Remove User from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index');
    }

    /**
     * Log in as a different user.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function loginAs(User $user)
    {
        $this->authorize('loginAs', $user);

        $user = $user;
        Auth::login($user);

        return redirect()->to('/');
    }
}
