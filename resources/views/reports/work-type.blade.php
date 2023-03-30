@extends('layouts.app')

@section('title', __('app.menu_title_reports'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_reports') }}</h1>
</header>

@if ($has_timer_items)
<div class="alert alert-warning text-black" role="alert">
    <p class="mb-0"><i class="zmdi zmdi-alert-triangle zmdi-hc-fw"></i> {{ __('app.reports_index_warning_runningtimeritems') }}</p>
</div>
@endif

@if ($has_unlocked_items)
<div class="alert alert-warning text-black" role="alert">
    <p class="mb-0"><i class="zmdi zmdi-alert-triangle zmdi-hc-fw"></i> {{ __('app.reports_index_warning_itemsnotlocked') }}</p>
</div>
@endif

@if ($has_too_long_items)
<div class="alert alert-warning text-black" role="alert">
    <p class="mb-0"><i class="zmdi zmdi-alert-triangle zmdi-hc-fw"></i> {{ __('app.reports_index_warning_itemsspanoverday') }}</p>
</div>
@endif

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.reports_index_options_card_title') }}</h4>

        <form action="" method="get">
            <div class="report-row row align-items-center">
                <div class="col">
                    <div class="form-group">
                        <label for="work_type_id">{{ __('app.reports_index_options_worktype_label') }}</label>
                        <div class="select">
                            <select name="work_type_id" id="work_type_id" class="form-control select2">
                                @foreach ($work_types_select as $key => $value)
                                <option value="{{ $key }}" {{ $key == $current_work_type ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="week">{{ __('app.reports_index_options_week_label') }}</label>
                        <div class="select">
                            <select name="week" id="week" class="form-control select2">
                                <option value="">{{ __('app.global_select_prepend_optional') }}</option>
                                @foreach ($date_selects['week'] as $key => $value)
                                <option value="{{ $key }}" {{ $key == $current_week ? 'selected="selected"' : '' }}>{{ __('app.reports_index_display_weekly_table_week_text', ['week' => $value]) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="month">{{ __('app.reports_index_options_month_label') }}</label>
                        <div class="select">
                            <select name="month" id="month" class="form-control select2">
                                <option value="">{{ __('app.global_select_prepend_optional') }}</option>
                                @foreach ($date_selects['month'] as $key => $value)
                                <option value="{{ $key }}" {{ $key == $current_month ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        <label for="year">{{ __('app.reports_index_options_year_label') }}</label>
                        <div class="select">
                            <select name="year" id="year" class="form-control select2">
                                <option value="">{{ __('app.global_select_prepend_optional') }}</option>
                                @foreach ($date_selects['year'] as $key => $value)
                                <option value="{{ $key }}" {{ $key == $current_year ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-1">{{ __('app.reports_index_options_generate_button') }}</button>
                    <div class="dropdown d-inline">
                        <button class="btn btn-primary mb-1 @if($active_item > 0) active @endif" data-toggle="dropdown">{{ __('app.reports_index_options_period_button') }}</button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="{{ route('reports.work-type', ['work_type_id' => $current_work_type, 'month' => $now->format('n'), 'year' => $now->format('Y')]) }}" class="dropdown-item @if($active_item == 1) active @endif">{{ __('app.reports_index_options_period_thismonth_button') }}</a>
                            <a href="{{ route('reports.work-type', ['work_type_id' => $current_work_type, 'month' => $last_month->format('n'), 'year' => $last_month->format('Y')]) }}" class="dropdown-item @if($active_item == 2) active @endif">{{ __('app.reports_index_options_period_lastmonth_button') }}</a>
                            <a href="{{ route('reports.work-type', ['work_type_id' => $current_work_type, 'week' => $now->format('o\WW')]) }}" class="dropdown-item @if($active_item == 3) active @endif">{{ __('app.reports_index_options_period_thisweek_button') }}</a>
                            <a href="{{ route('reports.work-type', ['work_type_id' => $current_work_type, 'week' => $last_week->format('o\WW')]) }}" class="dropdown-item @if($active_item == 4) active @endif">{{ __('app.reports_index_options_period_lastweek_button') }}</a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('reports.work-type', ['work_type_id' => $current_work_type, 'all' => 1]) }}" class="dropdown-item @if($active_item == 5) active @endif">{{ __('app.reports_index_options_period_showall_button') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if (!empty($current_work_type))
<div class="row quick-stats">
    <div class="col">
        <div class="quick-stats__item bg-primary">
            <div class="quick-stats__info">
                <h2>{{ secondsToDecimal($total_time) }}</h2>
                <small>{{ __('app.reports_index_quickstats_totaltime_label') }}</small>
            </div>
        </div>
    </div>

    @can('show.money')
    <div class="col">
        <div class="quick-stats__item bg-primary">
            <div class="quick-stats__info">
                <h2>{{ __('app.global_money_format', ['rate' => number_format($total_rate, 2)]) }}</h2>
                <small>{{ __('app.reports_index_quickstats_totalrate_label') }}</small>
            </div>
        </div>
    </div>
    @endcan
</div>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.reports_index_display_card_title') }}</h4>

        <div class="tab-container">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-daily" role="tab">{{ __('app.reports_index_display_daily_table_title') }} ({{ $total_entries }})</a>
                </li>
                @if (count($entries_weekly) > 1)
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-weekly" role="tab">{{ __('app.reports_index_display_weekly_table_title') }} ({{ count($entries_weekly) }})</a>
                </li>
                @endif
                @if (count($entries_monthly) > 1)
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-monthly" role="tab">{{ __('app.reports_index_display_monthly_table_title') }} ({{ count($entries_monthly) }})</a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-project" role="tab">{{ __('app.reports_index_display_timebyproject_table_title') }} ({{ count($entries_by_project) }})</a>
                </li>
                @can('show.reports.others')
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-user" role="tab">{{ __('app.reports_index_display_timebyuser_table_title') }} ({{ count($entries_by_user) }})</a>
                </li>
                @endcan
            </ul>

            <div class="tab-content pb-0">
                <div class="tab-pane active fade show" id="tab-daily" role="tabpanel">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('app.reports_index_display_daily_table_day_column') }}</th>
                                <th>{{ __('app.reports_index_display_daily_table_time_column') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries_daily as $key => $value)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>
                                    @if ($value['time_worked'] > 86400)
                                    <i class="zmdi zmdi-time text-danger mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemspansoverday') }}"></i>
                                    @endif
                                    @if ($value['has_timer'])
                                    <i class="zmdi zmdi-timer text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemhastimer') }}"></i>
                                    @endif
                                    @if ($value['has_unlocked'])
                                    <i class=" zmdi zmdi-lock-outline text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemnotlocked') }}"></i>
                                    @endif
                                    {{ secondsToDecimal($value['time_worked']) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2">{{ __('app.reports_index_display_weekly_table_noentries_text') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (count($entries_weekly) > 1)
                <div class="tab-pane fade" id="tab-weekly" role="tabpanel">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('app.reports_index_display_weekly_table_day_column') }}</th>
                                <th>{{ __('app.reports_index_display_weekly_table_time_column') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries_weekly as $week => $item)
                            <tr class="bg-light">
                                <td>{{ __('app.reports_index_display_weekly_table_week_text', ['week' => $week]) }}</td>
                                <td>
                                    @if ($item['has_timer'])
                                    <i class="zmdi zmdi-timer text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemshavetimer') }}"></i>
                                    @endif
                                    @if ($item['has_unlocked'])
                                    <i class=" zmdi zmdi-lock-outline text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemsnotlocked') }}"></i>
                                    @endif
                                    {{ secondsToDecimal($item['time_worked']) }}
                                </td>
                            </tr>
                            @foreach (filterReportsArray($item) as $key => $value)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>
                                    @if ($value['time_worked'] > 86400)
                                    <i class="zmdi zmdi-time text-danger mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemspansoverday') }}"></i>
                                    @endif
                                    @if ($value['has_timer'])
                                    <i class="zmdi zmdi-timer text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemhastimer') }}"></i>
                                    @endif
                                    @if ($value['has_unlocked'])
                                    <i class=" zmdi zmdi-lock-outline text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemnotlocked') }}"></i>
                                    @endif
                                    {{ secondsToDecimal($value['time_worked']) }}
                                </td>
                            </tr>
                            @endforeach
                            @empty
                            <tr>
                                <td colspan="2">{{ __('app.reports_index_display_weekly_table_noentries_text') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                @if (count($entries_monthly) > 1)
                <div class="tab-pane fade" id="tab-monthly" role="tabpanel">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('app.reports_index_display_monthly_table_day_column') }}</th>
                                <th>{{ __('app.reports_index_display_monthly_table_time_column') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries_monthly as $month => $item)
                            <tr class="bg-light">
                                <td>{{ __('app.reports_index_display_monthly_table_month_text', ['month' => $month]) }}</td>
                                <td>
                                    @if ($item['has_timer'])
                                    <i class="zmdi zmdi-timer text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemshavetimer') }}"></i>
                                    @endif
                                    @if ($item['has_unlocked'])
                                    <i class=" zmdi zmdi-lock-outline text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemsnotlocked') }}"></i>
                                    @endif
                                    {{ secondsToDecimal($item['time_worked']) }}
                                </td>
                            </tr>
                            @foreach (filterReportsArray($item) as $key => $value)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>
                                    @if ($value['time_worked'] > 86400)
                                    <i class="zmdi zmdi-time text-danger mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemspansoverday') }}"></i>
                                    @endif
                                    @if ($value['has_timer'])
                                    <i class="zmdi zmdi-timer text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemhastimer') }}"></i>
                                    @endif
                                    @if ($value['has_unlocked'])
                                    <i class=" zmdi zmdi-lock-outline text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemnotlocked') }}"></i>
                                    @endif
                                    {{ secondsToDecimal($value['time_worked']) }}
                                </td>
                            </tr>
                            @endforeach
                            @empty
                            <tr>
                                <td colspan="2">{{ __('app.reports_index_display_monthly_table_noentries_text') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                <div class="tab-pane fade" id="tab-project" role="tabpanel">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('app.reports_index_display_timebyproject_table_project_column') }}</th>
                                <th>{{ __('app.reports_index_display_timebyproject_table_time_column') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries_by_project as $key)
                            <tr>
                                <td>{{ $key['name'] }}</td>
                                <td>
                                    @if ($key['has_timer'])
                                    <i class="zmdi zmdi-timer text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemhastimer') }}"></i>
                                    @endif
                                    @if ($key['has_unlocked'])
                                    <i class=" zmdi zmdi-lock-outline text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemnotlocked') }}"></i>
                                    @endif
                                    {{ secondsToDecimal($key['time_worked']) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2">{{ __('app.reports_index_display_timebyproject_table_noentries_text') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @can('show.reports.others')
                <div class="tab-pane fade" id="tab-user" role="tabpanel">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('app.reports_index_display_timebyuser_table_user_column') }}</th>
                                <th>{{ __('app.reports_index_display_timebyuser_table_time_column') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries_by_user as $key)
                            <tr>
                                <td>{{ $key['name'] }}</td>
                                <td>
                                    @if ($key['has_timer'])
                                    <i class="zmdi zmdi-timer text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemhastimer') }}"></i>
                                    @endif
                                    @if ($key['has_unlocked'])
                                    <i class=" zmdi zmdi-lock-outline text-warning mr-1" data-toggle="tooltip" title="{{ __('app.reports_index_warning_itemnotlocked') }}"></i>
                                    @endif
                                    {{ secondsToDecimal($key['time_worked']) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2">{{ __('app.reports_index_display_timebyuser_table_noentries_text') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>
@endif
@endsection