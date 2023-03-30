@extends('layouts.app')

@section('title', __('app.menu_title_users'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_users') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.users_show_card_title') }}</h4>

        <dl class="row">
            <dt class="col-sm-3 text-truncate">{{ __('app.users_show_name_label') }}</dt>
            <dd class="col-sm-9">{{ $user->name }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.users_show_email_label') }}</dt>
            <dd class="col-sm-9">{{ $user->email }}</dd>

            @can('show.money')
            <dt class="col-sm-3 text-truncate">{{ __('app.users_show_hourlyrate_label') }}</dt>
            <dd class="col-sm-9">{{ __('app.global_money_format', ['rate' => number_format($user->hourly_rate, 2)]) }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.users_show_travelexpenses_label') }}</dt>
            <dd class="col-sm-9">{{ __('app.global_money_format', ['rate' => number_format($user->travel_expenses, 2)]) }}</dd>
            @endcan

            <dt class="col-sm-3 text-truncate">{{ __('app.users_show_timezone_label') }}</dt>
            <dd class="col-sm-9">{{ $user->timezone }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.users_show_timeular_label') }}</dt>
            <dd class="col-sm-9">{{ $user->timeular_id ?? __('app.users_show_timeular_value_empty') }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.users_show_role_label') }}</dt>
            @if ($user->is_active)
            <dd class="col-sm-9"><a href="{{ route('roles.show', $user->roles->first()->id) }}">{{ $user->roles->first()->name }}</a></dd>
            @else
            <dd class="col-sm-9">{{ __('app.users_show_inactiveuser_label') }}</dd>
            @endif

            <dt class="col-sm-3 text-truncate">{{ __('app.users_show_api_label') }}</dt>
            <dd class="col-sm-9"><code>{{ $user->api_token ?? __('app.users_show_api_value_empty') }}</code></dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.users_show_entries_label') }}</dt>
            <dd class="col-sm-9"><a href="{{ route('time-entries.index', ['user_id' => $user->id]) }}">{{ __('app.users_show_entriesuser_label') }}</a></dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.users_show_reports_label') }}</dt>
            <dd class="col-sm-9"><a href="{{ route('reports.user', ['user_id' => $user->id, 'all' => true]) }}">{{ __('app.users_show_reportalltime_label') }}</a></dd>
            <dd class="col-sm-9 offset-sm-3"><a href="{{ route('reports.user', ['user_id' => $user->id, 'month' => date('n', strtotime('first day of previous month')), 'year' => date('Y', strtotime('first day of previous month'))]) }}">{{ __('app.users_show_reportlastmonth_label') }}</a></dd>
            <dd class="col-sm-9 offset-sm-3"><a href="{{ route('reports.user', ['user_id' => $user->id, 'month' => date('n'), 'year' => date('Y')]) }}">{{ __('app.users_show_reportthismonth_label') }}</a></dd>
        </dl>

        <hr class="mt-3 mb-4">

        <a href="{{ route('users.index') }}" class="btn btn-primary">{{ __('app.users_show_card_link_back') }}</a>
        @can('edit.users')
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">{{ __('app.users_show_card_link_edit') }}</a>
        @endcan
        @can('login_as.users')
        @if ($user->id != Auth::user()->id)
        <a href="{{ route('users.login-as', $user->id) }}" class="btn btn-warning text-black">{{ __('app.users_show_card_link_loginas') }}</a>
        @else
        <button class="btn btn-warning text-black" disabled="disabled">{{ __('app.users_show_card_link_loginas') }}</button>
        @endif
        @endcan
        @can('delete.users')
        <a class="btn btn-danger text-black" data-toggle="modal" data-target="#modal-confirm-delete">{{ __('app.users_show_card_link_delete') }}</a>
        @endcan
    </div>
</div>

@can('delete.users')
<div class="modal fade" id="modal-confirm-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pull-left">{{ __('app.users_show_modal_delete_title') }}</h5>
            </div>
            <div class="modal-body">{{ __('app.users_show_modal_delete_body') }}</div>
            <div class="modal-footer">
                <form action="{{ route('users.destroy', $user->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <input type="submit" value="{{ __('app.users_show_table_delete_button') }}" class="btn btn-danger text-black text-uppercase">
                </form>
                <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.users_show_modal_close_button') }}</button>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection
