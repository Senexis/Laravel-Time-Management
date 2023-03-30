@extends('layouts.app')

@section('title', __('app.menu_title_timeentries'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_timeentries') }}</h1>
</header>

<div class="row">
    <div class="col-lg-9">
        <div class="card">
            <div class="toolbar toolbar--inner">
                @if ($time_entries instanceof \Illuminate\Pagination\AbstractPaginator)
                <div class="toolbar__label">{{ trans_choice('app.timeentries_list_toolbar_label', $time_entries->total(), ['count' => $time_entries->count(), 'total' => $time_entries->total()]) }}</div>
                @else
                <div class="toolbar__label">{{ trans_choice('app.timeentries_list_toolbar_label', count($time_entries), ['count' => count($time_entries), 'total' => count($time_entries)]) }}</div>
                @endif

                <div class="actions">
                    @if ($create_time_entries)
                    <a href="{{ route('time-entries.create') }}" class="actions__item zmdi zmdi-plus zmdi-hc-fw" data-toggle="tooltip" title="{{ __('app.timeentries_list_card_link_create') }}"></a>
                    @endif
                    @if ($lock_time_entries_batch || $unlock_time_entries_batch)
                    <div class="dropdown actions__item" data-toggle="tooltip" title="{{ __('app.timeentries_list_card_link_selection') }}">
                        <i class="actions__item zmdi zmdi-check-circle zmdi-hc-fw" data-toggle="dropdown"></i>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu--icon">
                            <a id="select-all" class="dropdown-item"><i class="zmdi zmdi-check-all zmdi-hc-fw"></i> {{ __('app.timeentries_list_card_link_selectall') }}</a>
                            <a id="select-none" class="dropdown-item"><i class="zmdi zmdi-format-clear-all zmdi-hc-fw"></i> {{ __('app.timeentries_list_card_link_selectnone') }}</a>
                            <div class="dropdown-divider"></div>
                            <h6 class="dropdown-header"><span id="selected-count">0</span> {{ __('app.timeentries_list_table_selected_label') }}</h6>
                            @if ($lock_time_entries_batch)
                            <a class="dropdown-item" data-toggle="modal" data-target="#modal-confirm-lockall"><i class="zmdi zmdi-lock-outline zmdi-hc-fw"></i> {{ __('app.timeentries_list_card_link_lockselected') }}</a>
                            @endif
                            @if ($unlock_time_entries_batch)
                            <a class="dropdown-item" data-toggle="modal" data-target="#modal-confirm-unlockall"><i class="zmdi zmdi-lock-open zmdi-hc-fw"></i> {{ __('app.timeentries_list_card_link_unlockselected') }}</a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                @if ($lock_time_entries_batch)
                <div class="modal fade" id="modal-confirm-lockall" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title pull-left">{{ __('app.timeentries_list_modal_lockall_title') }}</h5>
                            </div>
                            @if ($unlock_time_entries_self)
                            <div class="modal-body">{{ __('app.timeentries_list_modal_lockall_body') }}</div>
                            @else
                            <div class="modal-body">{{ __('app.timeentries_list_modal_lockallwarning_body') }}</div>
                            @endif
                            <div class="modal-footer">
                                <form id="lockall-form" action="{{ route('time-entries.batchlock') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="entries" id="entries">

                                    @if ($unlock_time_entries_self)
                                    <input type="submit" value="{{ __('app.timeentries_list_table_lockall_button') }}" class="btn btn-light text-uppercase">
                                    @else
                                    <input type="submit" value="{{ __('app.timeentries_list_table_lockall_button') }}" class="btn btn-warning text-uppercase">
                                    @endif
                                </form>
                                <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.timeentries_list_modal_close_button') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if ($unlock_time_entries_batch)
                <div class="modal fade" id="modal-confirm-unlockall" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title pull-left">{{ __('app.timeentries_list_modal_unlockall_title') }}</h5>
                            </div>
                            <div class="modal-body">{{ __('app.timeentries_list_modal_unlockall_body') }}</div>
                            <div class="modal-footer">
                                <form id="unlockall-form" action="{{ route('time-entries.batchunlock') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="entries" id="entries">

                                    <input type="submit" value="{{ __('app.timeentries_list_table_unlockall_button') }}" class="btn btn-light text-uppercase">
                                </form>
                                <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.timeentries_list_modal_close_button') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="modal fade" id="modal-filters" tabindex="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="" method="get">
                                <div class="modal-header">
                                    <h5 class="modal-title pull-left">{{ __('app.timeentries_list_modal_filters_title') }}</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="amount">{{ __('app.timeentries_list_modal_filters_amount_label') }}</label>
                                        <div class="select">
                                            <select name="amount" id="amount" class="form-control select2">
                                                <option value="">{{ __('app.global_select_prepend') }}</option>
                                                <option value="15" {{ $current_amount == '15' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_amount15_option') }}</option>
                                                <option value="50" {{ $current_amount == '50' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_amount50_option') }}</option>
                                                <option value="100" {{ $current_amount == '100' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_amount100_option') }}</option>
                                                <option value="250" {{ $current_amount == '250' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_amount250_option') }}</option>
                                                <option value="-1" {{ $current_amount == '-1' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_amountall_option') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="from_time">{{ __('app.timeentries_list_modal_filters_fromtime_label') }}</label>
                                        <input type="text" name="from_time" id="from_time" class="form-control datetime-picker-custom" value="{{ $current_from_time }}" placeholder="{{ __('app.global_select_prepend') }}">
                                        <i class="form-group__bar"></i>
                                    </div>

                                    <div class="form-group">
                                        <label for="to_time">{{ __('app.timeentries_list_modal_filters_totime_label') }}</label>
                                        <input type="text" name="to_time" id="to_time" class="form-control datetime-picker-custom" value="{{ $current_to_time }}" placeholder="{{ __('app.global_select_prepend') }}">
                                        <i class="form-group__bar"></i>
                                    </div>

                                    <div class="form-group">
                                        <label for="type">{{ __('app.timeentries_list_modal_filters_type_label') }}</label>
                                        <div class="select">
                                            <select name="type" id="type" class="form-control select2">
                                                <option value="">{{ __('app.global_select_prepend') }}</option>
                                                <option value="unlocked" {{ $current_type == 'unlocked' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_unlocked_option') }}</option>
                                                <option value="locked" {{ $current_type == 'locked' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_locked_option') }}</option>
                                                <option value="timer" {{ $current_type == 'timer' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_timer_option') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="project_id">{{ __('app.timeentries_list_modal_filters_projectid_label') }}</label>
                                        <div class="select">
                                            <select name="project_id" id="project_id" class="form-control select2">
                                                @foreach ($projects_select as $key => $project)
                                                <option value="{{ $key }}" {{ $key == $current_project ? 'selected="selected"' : '' }}>{{ $project }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="work_type_id">{{ __('app.timeentries_list_modal_filters_worktypeid_label') }}</label>
                                        <div class="select">
                                            <select name="work_type_id" id="work_type_id" class="form-control select2">
                                                @foreach ($work_types_select as $key => $work_type)
                                                <option value="{{ $key }}" {{ $key == $current_work_type ? 'selected="selected"' : '' }}>{{ $work_type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    @if ($show_time_entries_others)
                                    <div class="form-group">
                                        <label for="user_id">{{ __('app.timeentries_list_modal_filters_userid_label') }}</label>
                                        <div class="select">
                                            <select name="user_id" id="user_id" class="form-control select2">
                                                @foreach ($users_select as $key => $user)
                                                <option value="{{ $key }}" {{ $key == $current_user ? 'selected="selected"' : '' }}>{{ $user }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endif

                                    <a href="{{ route('time-entries.index') }}" class="btn btn-outline-danger btn-block">{{ __('app.timeentries_list_modal_filters_clear_button') }}</a>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" class="btn btn-link" value="{{ __('app.timeentries_list_modal_apply_button') }}">
                                    <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.timeentries_list_modal_close_button') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="listview listview--bordered listview--hover">
                @forelse($time_entries as $time_entry)
                <div class="listview__item" @if ( !empty($time_entry->notes) ) data-toggle="popover" title="" data-placement="top" data-trigger="hover" data-content="{{ Str::limit($time_entry->notes, $limit = 280, $end = '...') }}" data-original-title="{{ __('app.timeentries_show_notes_label') }}" @endif>
                    @if ($time_entry->is_timer)
                    <div class="checkbox checkbox--char checkbox--disabled listview__img">
                        <label class="checkbox__char bg-warning text-black"><i class="zmdi zmdi-timer zmdi-hc-fw"></i></label>
                    </div>
                    @else
                    <div class="checkbox checkbox--char listview__img">
                        <input type="checkbox" id="select-checkbox-{{ $time_entry->id }}" class="select-checkbox" tabindex="-1">
                        <label class="checkbox__char bg-primary" for="select-checkbox-{{ $time_entry->id }}">{{ $time_entry->project != null ? substr($time_entry->project->name, 0, 2) : '?' }}</label>
                    </div>
                    @endif

                    <div class="listview__content">
                        @if ($show_time_entries_self)
                        <div class="listview__heading text-truncate"><a href="{{ route('time-entries.show', $time_entry->id) }}">{{ $time_entry->project != null ? $time_entry->project->name : __('app.global_empty_short') }} &mdash; {{ $time_entry->work_type != null ? $time_entry->work_type->name : __('app.global_empty_short') }}</a></div>
                        <p class="listview__text">
                            @if (!$time_entry->is_valid) <span class="mr-2 text-danger"><i class="zmdi zmdi-alert-polygon zmdi-hc-fw"></i> {{ __('app.timeentries_list_invalid_alert') }}</span>
                            @endif
                            @if ($show_time_entries_others)
                            <span class="mr-2"><a href="{{ route('users.show', $time_entry->user->id) }}"><i class="zmdi zmdi-account zmdi-hc-fw"></i> {{ $time_entry->user->name }}</a></span>
                            @endif
                            @if ($show_money)
                            @if ($time_entry->total_wage > -1)
                            <span class="mr-2"><a href="{{ route('reports.user', ['user_id' => $time_entry->user->id, 'month' => $time_entry->start_time->month, 'year' => $time_entry->start_time->year]) }}"><i class="zmdi zmdi-balance zmdi-hc-fw"></i> {{ __('app.global_money_format', ['rate' => number_format($time_entry->total_wage, 2)]) }}</a></span>
                            @else
                            <span class="mr-2 text-danger"><i class="zmdi zmdi-balance zmdi-hc-fw"></i> {{ __('app.timeentries_list_nobalance_alert') }}</span>
                            @endif
                            @endif
                            @if ($time_entry->is_timer)
                            <span class="mr-2"><i class="zmdi zmdi-timer zmdi-hc-fw"></i> {{ $time_entry->start_time->format(__('app.global_time_format')) }}</span>
                            @else
                            <span class="mr-2"><i class="zmdi zmdi-time zmdi-hc-fw"></i> {{ $time_entry->start_time->format(__('app.global_time_format')) }} &mdash; {{ $time_entry->end_time->format(__('app.global_time_format')) }}</span>
                            @endif
                            <span class="mr-2"><i class="zmdi zmdi-time-interval zmdi-hc-fw"></i> {{ secondsToHms($time_entry->time_worked) }}</span>
                            @if (!empty($time_entry->notes))
                            <span class="mr-2"><i class="zmdi zmdi-comment-outline zmdi-hc-fw"></i></span>
                            @endif
                        </p>
                        @else
                        <div class="listview__heading text-truncate">{{ $time_entry->project != null ? $time_entry->project->name : __('app.global_empty_short') }} &mdash; {{ $time_entry->work_type != null ? $time_entry->work_type->name : __('app.global_empty_short') }}</div>
                        <p class="listview__text">
                            <span class="mr-2"><i class="zmdi zmdi-eye-off zmdi-hc-fw"></i></span>
                        </p>
                        @endif
                    </div>
                    <div class="actions listview__actions">
                        @if ($time_entry->is_timer)
                        @if ($stop_time_entries_self)
                        @if ($time_entry->is_running)
                        <a href="{{ route('time-entries.pause', $time_entry->id) }}" class="actions__item zmdi zmdi-pause zmdi-hc-fw" data-toggle="tooltip" title="{{ __('app.timeentries_list_table_pause_button') }}"></a>
                        @else
                        <a href="{{ route('time-entries.resume', $time_entry->id) }}" class="actions__item zmdi zmdi-play zmdi-hc-fw" data-toggle="tooltip" title="{{ __('app.timeentries_list_table_resume_button') }}"></a>
                        @endif
                        <a href="{{ route('time-entries.stop', $time_entry->id) }}" class="actions__item zmdi zmdi-stop zmdi-hc-fw" data-toggle="tooltip" title="{{ __('app.timeentries_list_table_stop_button') }}"></a>
                        @endif
                        @else
                        @if (!$time_entry->locked_at)
                        @if ($edit_time_entries_self)
                        <a href="{{ route('time-entries.edit', $time_entry->id) }}" class="actions__item zmdi zmdi-edit zmdi-hc-fw" data-toggle="tooltip" title="{{ __('app.timeentries_list_table_edit_button') }}"></a>
                        @endif
                        @endif
                        @if ($lock_time_entries_self)
                        @if ($unlock_time_entries_self)
                        @if ($time_entry->locked_at)
                        <a href="{{ route('time-entries.unlock', $time_entry->id) }}" class="actions__item zmdi zmdi-lock-open zmdi-hc-fw" data-toggle="tooltip" title="{{ __('app.timeentries_list_table_unlock_button') }}"></a>
                        @else
                        <a href="{{ route('time-entries.lock', $time_entry->id) }}" class="actions__item zmdi zmdi-lock-outline zmdi-hc-fw" data-toggle="tooltip" title="{{ __('app.timeentries_list_table_lock_button') }}"></a>
                        @endif
                        @else
                        @if (!$time_entry->locked_at)
                        <a class="actions__item zmdi zmdi-lock-outline text-warning zmdi-hc-fw" data-toggle="modal" data-target="#modal-confirm-lock-{{ $time_entry->id }}"></a>
                        @endif
                        @endif
                        @endif
                        @endif
                        @if ($show_time_entries_self || ($delete_time_entries_self && !$time_entry->locked_at) || ($edit_time_entries_self && !$time_entry->locked_at && $time_entry->is_timer))
                        <div class="dropdown actions__item">
                            <i class="actions__item zmdi zmdi-more-vert zmdi-hc-fw" data-toggle="dropdown"></i>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu--icon">
                                @if ($show_time_entries_self)
                                <a href="{{ route('time-entries.show', $time_entry->id) }}" class="dropdown-item"><i class="zmdi zmdi-eye zmdi-hc-fw"></i> {{ __('app.timeentries_list_table_view_button') }}</a>
                                @endif
                                @if (!$time_entry->locked_at)
                                @if ($edit_time_entries_self && $time_entry->is_timer)
                                <a href="{{ route('time-entries.edit', $time_entry->id) }}" class="dropdown-item"><i class="zmdi zmdi-edit zmdi-hc-fw"></i> {{ __('app.timeentries_list_table_edit_button') }}</a>
                                @endif
                                @if ($delete_time_entries_self)
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" data-toggle="modal" data-target="#modal-confirm-delete-{{ $time_entry->id }}"><i class="zmdi zmdi-delete zmdi-hc-fw"></i> {{ __('app.timeentries_list_table_delete_button') }}</a>
                                @endif
                                @endif
                            </div>
                        </div>
                        @endif
                        <span class="issue-tracker__tag bg-secondary d-none d-md-inline">{{ $time_entry->start_time->format(__('app.global_date_format')) }}</span>
                    </div>

                    @if (!$time_entry->locked_at && $lock_time_entries_self && !$unlock_time_entries_self)
                    <div class="modal fade" id="modal-confirm-lock-{{ $time_entry->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title pull-left">{{ __('app.timeentries_list_modal_lock_title') }}</h5>
                                </div>
                                <div class="modal-body">{{ __('app.timeentries_list_modal_lock_body') }}</div>
                                <div class="modal-footer">
                                    <a href="{{ route('time-entries.lock', $time_entry->id) }}" class="btn btn-warning text-uppercase">{{ __('app.timeentries_list_table_lock_button') }}</a>
                                    <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.timeentries_list_modal_close_button') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if ($delete_time_entries_self)
                    <div class="modal fade" id="modal-confirm-delete-{{ $time_entry->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title pull-left">{{ __('app.timeentries_list_modal_delete_title') }}</h5>
                                </div>
                                <div class="modal-body">{{ __('app.timeentries_list_modal_delete_body') }}</div>
                                <div class="modal-footer">
                                    <form action="{{ route('time-entries.destroy', $time_entry->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')

                                        <input type="submit" value="{{ __('app.timeentries_list_table_delete_button') }}" class="btn btn-danger text-black text-uppercase">
                                    </form>
                                    <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.timeentries_list_modal_close_button') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <div class="listview__item">
                    <div class="listview__content">
                        <div class="listview__heading">{{ __('app.timeentries_list_table_noresults_title') }}</div>
                        <p>{{ __('app.timeentries_list_table_noresults_body') }}</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        @if ($time_entries instanceof \Illuminate\Pagination\AbstractPaginator)
        {{ $time_entries->appends(request()->except('page'))->onEachSide(2)->links() }}
        @endif
    </div>
    <div class="col-lg-3 mt-4 mt-lg-0">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ __('app.timeentries_list_modal_filters_title') }}</h4>
                <form action="" method="get">
                    <div class="form-group">
                        <label for="amount">{{ __('app.timeentries_list_modal_filters_amount_label') }}</label>
                        <div class="select">
                            <select name="amount" id="amount" class="form-control select2">
                                <option value="">{{ __('app.global_select_prepend') }}</option>
                                <option value="15" {{ $current_amount == '15' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_amount15_option') }}</option>
                                <option value="50" {{ $current_amount == '50' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_amount50_option') }}</option>
                                <option value="100" {{ $current_amount == '100' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_amount100_option') }}</option>
                                <option value="250" {{ $current_amount == '250' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_amount250_option') }}</option>
                                <option value="-1" {{ $current_amount == '-1' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_amountall_option') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="from_time">{{ __('app.timeentries_list_modal_filters_fromtime_label') }}</label>
                        <input type="text" name="from_time" id="from_time" class="form-control datetime-picker-custom" value="{{ $current_from_time }}" placeholder="{{ __('app.global_select_prepend') }}">
                        <i class="form-group__bar"></i>
                    </div>

                    <div class="form-group">
                        <label for="to_time">{{ __('app.timeentries_list_modal_filters_totime_label') }}</label>
                        <input type="text" name="to_time" id="to_time" class="form-control datetime-picker-custom" value="{{ $current_to_time }}" placeholder="{{ __('app.global_select_prepend') }}">
                        <i class="form-group__bar"></i>
                    </div>

                    <div class="form-group">
                        <label for="type">{{ __('app.timeentries_list_modal_filters_type_label') }}</label>
                        <div class="select">
                            <select name="type" id="type" class="form-control select2">
                                <option value="">{{ __('app.global_select_prepend') }}</option>
                                <option value="unlocked" {{ $current_type == 'unlocked' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_unlocked_option') }}</option>
                                <option value="locked" {{ $current_type == 'locked' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_locked_option') }}</option>
                                <option value="timer" {{ $current_type == 'timer' ? 'selected="selected"' : '' }}>{{ __('app.timeentries_list_modal_filters_timer_option') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="project_id">{{ __('app.timeentries_list_modal_filters_projectid_label') }}</label>
                        <div class="select">
                            <select name="project_id" id="project_id" class="form-control select2">
                                @foreach ($projects_select as $key => $project)
                                <option value="{{ $key }}" {{ $key == $current_project ? 'selected="selected"' : '' }}>{{ $project }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="work_type_id">{{ __('app.timeentries_list_modal_filters_worktypeid_label') }}</label>
                        <div class="select">
                            <select name="work_type_id" id="work_type_id" class="form-control select2">
                                @foreach ($work_types_select as $key => $work_type)
                                <option value="{{ $key }}" {{ $key == $current_work_type ? 'selected="selected"' : '' }}>{{ $work_type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if ($show_time_entries_others)
                    <div class="form-group">
                        <label for="user_id">{{ __('app.timeentries_list_modal_filters_userid_label') }}</label>
                        <div class="select">
                            <select name="user_id" id="user_id" class="form-control select2">
                                @foreach ($users_select as $key => $user)
                                <option value="{{ $key }}" {{ $key == $current_user ? 'selected="selected"' : '' }}>{{ $user }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <a href="{{ route('time-entries.index') }}" class="btn btn-outline-secondary btn-block">{{ __('app.timeentries_list_modal_filters_clear_button') }}</a>
                    <input type="submit" class="btn btn-outline-primary btn-block" value="{{ __('app.timeentries_list_modal_apply_button') }}">
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ __('app.timeentries_list_modal_exports_title') }}</h4>
                <h6 class="card-subtitle">{{ __('app.timeentries_list_modal_exports_subtitle') }}</h6>

                <a href="{{ route('time-entries.export-csv', request()->input()) }}" class="btn btn-outline-secondary btn-block" download>{{ __('app.timeentries_list_modal_export_button') }}</a>
            </div>
        </div>
    </div>
</div>

@if ($create_time_entries)
<a href="{{ route('time-entries.create') }}" class="btn btn-primary btn--action zmdi zmdi-plus zmdi-hc-fw"></a>
@endif

@endsection

@push('scripts')
<script type="text/javascript">
    function updateCount(update = false, state = false) {
        var count = 0;
        var ids = [];

        $('.select-checkbox').each(function(i) {
            if (update) {
                $(this).prop("checked", state);
            }

            if ($(this).prop("checked")) {
                count++;
                ids.push($(this)[0].id.replace('select-checkbox-', ''));
            }
        });

        $('#selected-count').html(count);
        $('#lockall-form #entries, #unlockall-form #entries').val(ids.join(','));
    }

    $('#select-all').click(function(e) {
        e.preventDefault();
        updateCount(true, true);
    });

    $('.select-checkbox').click(function(e) {
        updateCount();
    });

    $('#select-none').click(function(e) {
        e.preventDefault();
        updateCount(true, false);
    });

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
