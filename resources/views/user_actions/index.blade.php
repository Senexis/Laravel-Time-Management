@extends('layouts.app')

@section('title', __('app.menu_title_useractions'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_useractions') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.useractions_list_card_title') }}</h4>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('app.useractions_list_table_user_column') }}</th>
                    <th>{{ __('app.useractions_list_table_action_column') }}</th>
                    <th>{{ __('app.useractions_list_table_model_column') }}</th>
                    <th>{{ __('app.useractions_list_table_id_column') }}</th>
                    <th>{{ __('app.useractions_list_table_date_column') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user_actions as $user_action)
                <tr>
                    <td>{{ $user_action->user->name }}</td>
                    <td>{{ $user_action->action }}</td>
                    <td>{{ $user_action->action_model }}</td>
                    <td>{{ $user_action->action_id }}</td>
                    <td>{{ $user_action->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if ($user_actions instanceof \Illuminate\Pagination\AbstractPaginator)
{{ $user_actions->appends(request()->except('page'))->onEachSide(2)->links() }}
@endif

@endsection
