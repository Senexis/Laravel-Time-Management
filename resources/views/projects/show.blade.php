@extends('layouts.app')

@section('title', __('app.menu_title_projects'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_projects') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.projects_show_card_title') }}</h4>

        <dl class="row">
            <dt class="col-sm-3 text-truncate">{{ __('app.projects_show_name_label') }}</dt>
            <dd class="col-sm-9">{{ $project->name }}</dd>

            @can('show.money')
            @if ($project->total_wage > -1)
            <dt class="col-sm-3 text-truncate">{{ __('app.projects_show_totalwage_label') }}</dt>
            <dd class="col-sm-9">{{ __('app.global_money_format', ['rate' => number_format($project->total_wage, 2)]) }}</dd>
            @endif
            @endcan

            <dt class="col-sm-3 text-truncate">{{ __('app.projects_show_totaltime_label') }}</dt>
            <dd class="col-sm-9">{{ secondsToHms($project->time_worked) }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.projects_show_timesused_label') }}</dt>
            <dd class="col-sm-9">{{ number_format($project->time_entries->count()) }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.projects_show_entries_label') }}</dt>
            <dd class="col-sm-9"><a href="{{ route('time-entries.index', ['project_id' => $project->id]) }}">{{ __('app.projects_show_entriesproject_label') }}</a></dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.projects_show_reports_label') }}</dt>
            <dd class="col-sm-9"><a href="{{ route('reports.project', ['project_id' => $project->id, 'all' => true]) }}">{{ __('app.projects_show_reportalltime_label') }}</a></dd>
            <dd class="col-sm-9 offset-sm-3"><a href="{{ route('reports.project', ['project_id' => $project->id, 'month' => date('n', strtotime('first day of previous month')), 'year' => date('Y', strtotime('first day of previous month'))]) }}">{{ __('app.projects_show_reportlastmonth_label') }}</a></dd>
            <dd class="col-sm-9 offset-sm-3"><a href="{{ route('reports.project', ['project_id' => $project->id, 'month' => date('n'), 'year' => date('Y')]) }}">{{ __('app.projects_show_reportthismonth_label') }}</a></dd>
        </dl>

        <hr class="mt-3 mb-4">

        <a href="{{ route('projects.index') }}" class="btn btn-primary">{{ __('app.projects_show_card_link_back') }}</a>
        @can('edit.projects')
        <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-primary">{{ __('app.projects_show_card_link_edit') }}</a>
        @endcan
        @can('delete.projects')
        <a class="btn btn-danger text-black" data-toggle="modal" data-target="#modal-confirm-delete">{{ __('app.projects_show_card_link_delete') }}</a>
        @endcan
    </div>
</div>

@can('delete.projects')
<div class="modal fade" id="modal-confirm-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pull-left">{{ __('app.projects_show_modal_delete_title') }}</h5>
            </div>
            <div class="modal-body">{{ __('app.projects_show_modal_delete_body') }}</div>
            <div class="modal-footer">
                <form action="{{ route('projects.destroy', $project->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <input type="submit" value="{{ __('app.projects_show_table_delete_button') }}" class="btn btn-danger text-black text-uppercase">
                </form>
                <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.projects_show_modal_close_button') }}</button>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection
