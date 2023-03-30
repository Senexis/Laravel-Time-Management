<?php

namespace App\Http\Controllers;

use App\UserAction;

class UserActionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show.user_actions');
    }

    /**
     * Display a listing of UserAction.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_actions = UserAction::with('user:id,name,email')->latest()->paginate(30);
        return view('user_actions.index', ['user_actions' => $user_actions]);
    }
}
