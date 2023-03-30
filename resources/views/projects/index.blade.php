@extends('layouts.app')

@section('title', __('app.menu_title_projects'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_projects') }}</h1>
</header>

<div class="row">
    <div class="col-lg-9">
        <div class="card">
            <div class="toolbar toolbar--inner">
                @if ($projects instanceof \Illuminate\Pagination\AbstractPaginator)
                <div class="toolbar__label">{{ trans_choice('app.projects_list_toolbar_label', $projects->total(), ['count' => $projects->count(), 'total' => $projects->total()]) }}</div>
                @else
                <div class="toolbar__label">{{ trans_choice('app.projects_list_toolbar_label', count($projects), ['count' => count($projects), 'total' => count($projects)]) }}</div>
                @endif

                <div class="actions">
                    @if ($create_projects)
                    <a href="{{ route('projects.create') }}" class="actions__item zmdi zmdi-plus zmdi-hc-fw" data-toggle="tooltip" title="{{ __('app.projects_list_card_link_create') }}"></a>
                    @endif
                </div>
            </div>

            <div class="listview listview--bordered listview--hover">
                @forelse($projects as $project)
                <div class="listview__item">
                    <i class="avatar-char bg-primary">{{ substr($project->name, 0, 2) }}</i>

                    <div class="listview__content">
                        @if ($show_projects)
                        <div class="listview__heading text-truncate"><a href="{{ route('projects.show', $project->id) }}">{{ $project->name }}</a></div>
                        <p>
                            @if ($show_money)
                            @if ($project->total_wage > -1)
                            <span class="mr-2"><a href="{{ route('reports.project', ['project_id' => $project->id]) }}"><i class="zmdi zmdi-balance zmdi-hc-fw"></i> {{ __('app.global_money_format', ['rate' => number_format($project->total_wage, 2)]) }}</a></span>
                            @endif
                            @endif
                            <span class="mr-2"><i class="zmdi zmdi-time-interval zmdi-hc-fw"></i> {{ secondsToHms($project->time_worked) }}</span>
                        </p>
                        @else
                        <div class="listview__heading text-truncate">{{ $project->name }}</div>
                        <p class="listview__text">
                            <span class="mr-2"><i class="zmdi zmdi-eye-off zmdi-hc-fw"></i></span>
                        </p>
                        @endif
                    </div>

                    <div class="actions listview__actions">
                        @if ($edit_projects)
                        <a href="{{ route('projects.edit', $project->id) }}" class="actions__item zmdi zmdi-edit zmdi-hc-fw" data-toggle="tooltip" title="{{ __('app.projects_list_table_edit_button') }}"></a>
                        @endif
                        @if ($show_projects || $delete_projects)
                        <div class="dropdown actions__item">
                            <i class="actions__item zmdi zmdi-more-vert zmdi-hc-fw" data-toggle="dropdown"></i>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu--icon">
                                @if ($show_projects)
                                <a href="{{ route('projects.show', $project->id) }}" class="dropdown-item"><i class="zmdi zmdi-eye zmdi-hc-fw"></i> {{ __('app.projects_list_table_view_button') }}</a>
                                @endif
                                @if ($delete_projects)
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" data-toggle="modal" data-target="#modal-confirm-delete-{{ $project->id }}"><i class="zmdi zmdi-delete zmdi-hc-fw"></i> {{ __('app.projects_list_table_delete_button') }}</a>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if ($delete_projects)
                        <div class="modal fade" id="modal-confirm-delete-{{ $project->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title pull-left">{{ __('app.projects_list_modal_delete_title') }}</h5>
                                    </div>
                                    <div class="modal-body">{{ __('app.projects_list_modal_delete_body') }}</div>
                                    <div class="modal-footer">
                                        <form action="{{ route('projects.destroy', $project->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')

                                            <input type="submit" value="{{ __('app.projects_list_table_delete_button') }}" class="btn btn-danger text-black text-uppercase">
                                        </form>
                                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.projects_list_modal_close_button') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="listview__item">
                    <div class="listview__content">
                        <div class="listview__heading">{{ __('app.projects_list_table_noresults_title') }}</div>
                        <p>{{ __('app.projects_list_table_noresults_body') }}</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        @if ($projects instanceof \Illuminate\Pagination\AbstractPaginator)
        {{ $projects->appends(request()->except('page'))->onEachSide(2)->links() }}
        @endif
    </div>
    <div class="col-lg-3 mt-4 mt-lg-0">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ __('app.projects_list_modal_filters_title') }}</h4>
                <form action="" method="get">
                    <div class="form-group">
                        <label for="amount">{{ __('app.projects_list_modal_filters_amount_label') }}</label>
                        <div class="select">
                            <select name="amount" id="amount" class="form-control select2">
                                <option value="">{{ __('app.global_select_prepend') }}</option>
                                <option value="15" {{ $current_amount == '15' ? 'selected="selected"' : '' }}>{{ __('app.projects_list_modal_filters_amount15_option') }}</option>
                                <option value="50" {{ $current_amount == '50' ? 'selected="selected"' : '' }}>{{ __('app.projects_list_modal_filters_amount50_option') }}</option>
                                <option value="100" {{ $current_amount == '100' ? 'selected="selected"' : '' }}>{{ __('app.projects_list_modal_filters_amount100_option') }}</option>
                                <option value="250" {{ $current_amount == '250' ? 'selected="selected"' : '' }}>{{ __('app.projects_list_modal_filters_amount250_option') }}</option>
                                <option value="-1" {{ $current_amount == '-1' ? 'selected="selected"' : '' }}>{{ __('app.projects_list_modal_filters_amountall_option') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="from_time">{{ __('app.projects_list_modal_filters_fromtime_label') }}</label>
                        <input type="text" name="from_time" id="from_time" class="form-control datetime-picker-custom" value="{{ $current_from_time }}" placeholder="{{ __('app.global_select_prepend') }}">
                        <i class="form-group__bar"></i>
                    </div>

                    <div class="form-group">
                        <label for="to_time">{{ __('app.projects_list_modal_filters_totime_label') }}</label>
                        <input type="text" name="to_time" id="to_time" class="form-control datetime-picker-custom" value="{{ $current_to_time }}" placeholder="{{ __('app.global_select_prepend') }}">
                        <i class="form-group__bar"></i>
                    </div>

                    <div class="form-group">
                        <label for="type">{{ __('app.projects_list_modal_filters_type_label') }}</label>
                        <div class="select">
                            <select name="type" id="type" class="form-control select2">
                                <option value="">{{ __('app.global_select_prepend') }}</option>
                                <option value="used" {{ $current_type == 'used' ? 'selected="selected"' : '' }}>{{ __('app.projects_list_modal_filters_used_option') }}</option>
                                <option value="unused" {{ $current_type == 'unused' ? 'selected="selected"' : '' }}>{{ __('app.projects_list_modal_filters_unused_option') }}</option>
                            </select>
                        </div>
                    </div>

                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-block">{{ __('app.projects_list_modal_filters_clear_button') }}</a>
                    <input type="submit" class="btn btn-outline-primary btn-block" value="{{ __('app.projects_list_modal_apply_button') }}">
                </form>
            </div>
        </div>
    </div>
</div>

@if ($create_projects)
<a href="{{ route('projects.create') }}" class="btn btn-primary btn--action zmdi zmdi-plus zmdi-hc-fw"></a>
@endif

@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('.datetime-picker-custom').flatpickr({
            altFormat: "d M Y H:i",
            altInput: true,
            dateFormat: "Y-m-d H:i:S",
            enableTime: true,
            nextArrow: '<i class="zmdi zmdi-long-arrow-right zmdi-hc-fw" />',
            prevArrow: '<i class="zmdi zmdi-long-arrow-left zmdi-hc-fw" />',
            time_24hr: true
        });
    });
</script>
@endpush
