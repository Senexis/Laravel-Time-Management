@extends('layouts.app')

@section('title', __('app.menu_title_worktypes'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_worktypes') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.worktypes_show_card_title') }}</h4>

        <dl class="row">
            <dt class="col-sm-3 text-truncate">{{ __('app.worktypes_show_name_label') }}</dt>
            <dd class="col-sm-9">{{ $work_type->name }}</dd>

            @can('show.work_types.others')
            <dt class="col-sm-3 text-truncate">{{ __('app.worktypes_show_role_label') }}</dt>
            @if ($work_type->role instanceof \Spatie\Permission\Models\Role)
            <dd class="col-sm-9"><a href="{{ route('roles.show', $work_type->role->id) }}">{{ $work_type->role->name }}</a></dd>
            @else
            <dd class="col-sm-9"><a href="{{ route('roles.index') }}">{{ __('app.worktypes_show_role_empty') }}</a></dd>
            @endif
            @endcan

            @can('show.money')
            @if ($work_type->total_wage > -1)
            <dt class="col-sm-3 text-truncate">{{ __('app.worktypes_show_totalwage_label') }}</dt>
            <dd class="col-sm-9">{{ __('app.global_money_format', ['rate' => number_format($work_type->total_wage, 2)]) }}</dd>
            @endif
            @endcan

            <dt class="col-sm-3 text-truncate">{{ __('app.worktypes_show_totaltime_label') }}</dt>
            <dd class="col-sm-9">{{ secondsToHms($work_type->time_worked) }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.worktypes_show_timesused_label') }}</dt>
            <dd class="col-sm-9">{{ number_format($work_type->time_entries->count()) }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.worktypes_show_reports_label') }}</dt>
            <dd class="col-sm-9"><a href="{{ route('reports.work-type', ['work_type_id' => $work_type->id, 'month' => date('n', strtotime('first day of previous month')), 'year' => date('Y', strtotime('first day of previous month'))]) }}">{{ __('app.worktypes_show_reportlastmonth_label') }}</a></dd>
            <dd class="col-sm-9 offset-sm-3"><a href="{{ route('reports.work-type', ['work_type_id' => $work_type->id, 'month' => date('n'), 'year' => date('Y')]) }}">{{ __('app.worktypes_show_reportthismonth_label') }}</a></dd>
        </dl>

        <hr class="mt-3 mb-4">

        <a href="{{ route('work-types.index') }}" class="btn btn-primary">{{ __('app.worktypes_show_card_link_back') }}</a>
        @can('edit.work_types.self')
        <a href="{{ route('work-types.edit', $work_type->id) }}" class="btn btn-primary">{{ __('app.worktypes_show_card_link_edit') }}</a>
        @endcan
        @can('delete.work_types.self')
        <a class="btn btn-danger text-black" data-toggle="modal" data-target="#modal-confirm-delete">{{ __('app.worktypes_show_card_link_delete') }}</a>
        @endcan
    </div>
</div>

@can('delete.work_types.self')
<div class="modal fade" id="modal-confirm-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pull-left">{{ __('app.worktypes_show_modal_delete_title') }}</h5>
            </div>
            <div class="modal-body">{{ __('app.worktypes_show_modal_delete_body') }}</div>
            <div class="modal-footer">
                <form action="{{ route('work-types.destroy', $work_type->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <input type="submit" value="{{ __('app.worktypes_show_table_delete_button') }}" class="btn btn-danger text-black text-uppercase">
                </form>
                <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.worktypes_show_modal_close_button') }}</button>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection
