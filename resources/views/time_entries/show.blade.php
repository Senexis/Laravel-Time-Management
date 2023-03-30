@extends('layouts.app')

@section('title', __('app.menu_title_timeentries'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_timeentries') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.timeentries_show_card_title') }}</h4>

        <dl class="row">
            <dt class="col-sm-3 text-truncate">{{ __('app.timeentries_show_date_label') }}</dt>
            <dd class="col-sm-9">{{ $time_entry->start_time->format(__('app.global_date_format')) }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.timeentries_show_starttime_label') }}</dt>
            <dd class="col-sm-9">{{ $time_entry->start_time->format(__('app.global_time_format')) }}</dd>

            @if (!$time_entry->is_timer)
            <dt class="col-sm-3 text-truncate">{{ __('app.timeentries_show_endtime_label') }}</dt>
            <dd class="col-sm-9">{{ $time_entry->end_time->format(__('app.global_time_format')) }}</dd>
            @endif

            @can('show.money')
            @if ($time_entry->total_wage > -1)
            <dt class="col-sm-3 text-truncate">{{ __('app.timeentries_show_totalwage_label') }}</dt>
            <dd class="col-sm-9">{{ __('app.global_money_format', ['rate' => number_format($time_entry->total_wage, 2)]) }}</dd>
            @endif
            @endcan

            <dt class="col-sm-3 text-truncate">{{ __('app.timeentries_show_totaltime_label') }}</dt>
            <dd class="col-sm-9">{{ secondsToHms($time_entry->time_worked) }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.timeentries_show_project_label') }}</dt>
            <dd class="col-sm-9"><a href="{{ route('projects.show', $time_entry->project->id) }}">{{ $time_entry->project->name }}</a></dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.timeentries_show_worktype_label') }}</dt>
            <dd class="col-sm-9"><a href="{{ route('work-types.show', $time_entry->work_type->id) }}">{{ $time_entry->work_type->name }}</a></dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.timeentries_show_location_label') }}</dt>
            <dd class="col-sm-9"><a href="{{ route('user-locations.show', $time_entry->location->id) }}">{{ $time_entry->location->name }}</a></dd>

            @can('show.time_entries.others')
            <dt class="col-sm-3 text-truncate">{{ __('app.timeentries_show_user_label') }}</dt>
            <dd class="col-sm-9"><a href="{{ route('users.show', [ $time_entry->user->id ]) }}">{{ $time_entry->user->name }}</a></dd>
            @endcan
        </dl>

        @if ( !empty($time_entry->notes) )
        <hr>

        <div class="card-body__title">{{ __('app.timeentries_show_notes_label') }}</div>
        <p>{!! nl2br(e($time_entry->notes)) !!}</p>
        @endif

        <hr class="mt-3 mb-4">

        <a href="{{ route('time-entries.index') }}" class="btn btn-primary">{{ __('app.timeentries_show_card_link_back') }}</a>
        @if ($time_entry->is_timer)
        @can('stop.time_entries.self')
        @if ($time_entry->is_running)
        <a href="{{ route('time-entries.pause', $time_entry->id) }}" class="btn btn-primary">{{ __('app.timeentries_show_card_link_pause') }}</a>
        @else
        <a href="{{ route('time-entries.resume', $time_entry->id) }}" class="btn btn-primary">{{ __('app.timeentries_show_card_link_resume') }}</a>
        @endif
        <a href="{{ route('time-entries.stop', $time_entry->id) }}" class="btn btn-primary">{{ __('app.timeentries_show_card_link_stop') }}</a>
        @endcan
        @else
        @if (!$time_entry->locked_at)
        @can('edit.time_entries.self')
        <a href="{{ route('time-entries.edit', $time_entry->id) }}" class="btn btn-primary">{{ __('app.timeentries_show_card_link_edit') }}</a>
        @endif
        @endif
        @can('lock.time_entries.self')
        @can('unlock.time_entries.self')
        @if ($time_entry->locked_at)
        <a href="{{ route('time-entries.unlock', $time_entry->id) }}" class="btn btn-primary">{{ __('app.timeentries_show_card_link_unlock') }}</a>
        @else
        <a href="{{ route('time-entries.lock', $time_entry->id) }}" class="btn btn-primary">{{ __('app.timeentries_show_card_link_lock') }}</a>
        @endif
        @else
        @if (!$time_entry->locked_at)
        <a class="btn btn-warning text-black" data-toggle="modal" data-target="#modal-confirm-lock">{{ __('app.timeentries_show_card_link_lock') }}</a>
        @endif
        @endcan
        @endcan
        @endif
        @can('delete.time_entries.self')
        <a class="btn btn-danger text-black" data-toggle="modal" data-target="#modal-confirm-delete">{{ __('app.timeentries_show_card_link_delete') }}</a>
        @endcan
    </div>
</div>

@can('lock.time_entries.self')
@if (!$time_entry->locked_at)
<div class="modal fade" id="modal-confirm-lock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pull-left">{{ __('app.timeentries_show_modal_lock_title') }}</h5>
            </div>
            <div class="modal-body">{{ __('app.timeentries_show_modal_lock_body') }}</div>
            <div class="modal-footer">
                <a href="{{ route('time-entries.lock', $time_entry->id) }}" class="btn btn-warning text-uppercase">{{ __('app.timeentries_show_table_lock_button') }}</a>
                <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.timeentries_show_modal_close_button') }}</button>
            </div>
        </div>
    </div>
</div>
@endif
@endcan

@can('delete.time_entries.self')
<div class="modal fade" id="modal-confirm-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pull-left">{{ __('app.timeentries_show_modal_delete_title') }}</h5>
            </div>
            <div class="modal-body">{{ __('app.timeentries_show_modal_delete_body') }}</div>
            <div class="modal-footer">
                <form action="{{ route('time-entries.destroy', $time_entry->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <input type="submit" value="{{ __('app.timeentries_show_table_delete_button') }}" class="btn btn-danger text-black text-uppercase">
                </form>
                <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.timeentries_show_modal_close_button') }}</button>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection
