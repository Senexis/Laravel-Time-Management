<?php

namespace App\Http\Controllers;

use App\User;
use App\UserLocation;

use Carbon\Carbon;
use App\Http\Requests\StoreUserLocationsRequest;
use App\Http\Requests\UpdateUserLocationsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLocationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(UserLocation::class, 'user_location');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        $current_amount = $r->query('amount', null);
        $current_from_time = $r->query('from_time', null);
        $current_to_time = $r->query('to_time', null);
        $current_type = $r->query('type', 'used');
        $current_user = $r->query('user_id', null);

        $user_locations = UserLocation::with('user:id,name,travel_expenses')->orderBy('name');
        $amount = 15;

        if (!empty($current_amount) && is_numeric($current_amount)) {
            $amount = intval($current_amount);
        }

        $show_locations_others = Auth::user()->can('show.locations.others');

        try {
            if (!empty($current_from_time)) {
                $today = Carbon::now();
                $from = Carbon::createFromFormat('Y-m-d H:i:s', $current_from_time);

                $user_locations = $user_locations->whereHas('time_entries', function ($query) use ($today, $from) {
                    $query->whereBetween('start_time', [$from, $today]);
                });
            }

            if (!empty($current_to_time)) {
                $to = Carbon::createFromFormat('Y-m-d H:i:s', $current_to_time);

                $user_locations = $user_locations->whereHas('time_entries', function ($query) use ($to) {
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
                $user_locations = $user_locations->whereHas('time_entries');
            } else if ($current_type == 'unused') {
                $user_locations = $user_locations->whereDoesntHave('time_entries');
            }
        }

        if ($show_locations_others && !empty($current_user)) {
            $user_locations = $user_locations->where('user_id', $current_user);
        } else if ($show_locations_others && empty($current_user)) {
            // Go right through.
        } else {
            $user_locations = $user_locations->where('user_id', Auth::user()->id);
        }

        if ($amount > 0) {
            $user_locations = $user_locations->paginate($amount);
        } else {
            $user_locations = $user_locations->get();
        }

        $users_select = User::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id')->prepend(__('app.global_select_prepend'), '');

        $relations = [
            'wide_container' => true,

            'locations' => $user_locations,

            'current_amount' => $current_amount,
            'current_from_time' => $current_from_time,
            'current_to_time' => $current_to_time,
            'current_type' => $current_type,
            'current_user' => $current_user,

            'users_select' => $users_select,

            'create_locations' => Auth::user()->can('create.locations'),
            'delete_locations_self' => Auth::user()->can('delete.locations.self'),
            'edit_locations_self' => Auth::user()->can('edit.locations.self'),
            'show_locations_others' => $show_locations_others,
            'show_locations_self' => Auth::user()->can('show.locations.self'),
        ];

        return view('user_locations.index', $relations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user_locations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserLocationsRequest $request)
    {
        Auth::user()->locations()->create($request->all());

        return redirect()->route('user-locations.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserLocation  $user_location
     * @return \Illuminate\Http\Response
     */
    public function edit(UserLocation $user_location)
    {
        return view('user_locations.edit', ['location' => $user_location]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserLocation  $user_location
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserLocationsRequest $request, UserLocation $user_location)
    {
        $user_location->update($request->all());

        return redirect()->route('user-locations.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserLocation  $user_location
     * @return \Illuminate\Http\Response
     */
    public function show(UserLocation $user_location)
    {
        return view('user_locations.show', ['location' => $user_location]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserLocation  $user_location
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserLocation $user_location)
    {
        $user_location->delete();

        return redirect()->route('user-locations.index');
    }
}
