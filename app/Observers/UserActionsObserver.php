<?php

namespace App\Observers;

use App\UserAction;
use Illuminate\Support\Facades\Auth;

class UserActionsObserver
{
    public function saved($model)
    {
        if (!Auth::check()) return;

        if ($model->wasRecentlyCreated == true) {
            $action = 'created';
        } else {
            $action = 'updated';
        }

        UserAction::create([
            'user_id'      => Auth::user()->id,
            'action'       => $action,
            'action_model' => $model->getTable(),
            'action_id'    => $model->id
        ]);
    }


    public function deleting($model)
    {
        if (!Auth::check()) return;

        UserAction::create([
            'user_id'      => Auth::user()->id,
            'action'       => 'deleted',
            'action_model' => $model->getTable(),
            'action_id'    => $model->id
        ]);
    }
}
