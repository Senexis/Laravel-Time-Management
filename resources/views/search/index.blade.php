@extends('layouts.app')

@section('title', __('app.menu_title_search'))

@section('content')

<header class="content__title">
    <h1>{{ __('app.menu_title_search') }}</h1>
</header>

<div class="card results">
    <div class="tab-container">
        <div class="results__header">
            <form method="GET" action="{{ route('search.index') }}">
                <div class="results__search">
                    <input id="q" name="q" type="text" placeholder="{{ __('app.search_bar_placeholder') }}" value="{{ $current_query }}">
                </div>
            </form>

            <ul class="nav nav-tabs results__nav">
                @if ($list_locations_self)
                <li class="nav-item">
                    <a class="nav-link {{ $current_tab == 1 ? 'active' : '' }} {{ $locations_count > 0 ? '' : 'disabled' }}" data-toggle="tab" href="#tab-locations" role="tab">{{ trans_choice('app.search_tab_locations', $locations_count, ['count' => $locations_count . ($locations_count == 15 ? '+' : '')]) }}</a>
                </li>
                @endif

                @if ($list_projects)
                <li class="nav-item">
                    <a class="nav-link {{ $current_tab == 2 ? 'active' : '' }} {{ $projects_count > 0 ? '' : 'disabled' }}" data-toggle="tab" href="#tab-projects" role="tab">{{ trans_choice('app.search_tab_projects', $projects_count, ['count' => $projects_count . ($projects_count == 15 ? '+' : '')]) }}</a>
                </li>
                @endif

                @if ($list_roles)
                <li class="nav-item">
                    <a class="nav-link {{ $current_tab == 3 ? 'active' : '' }} {{ $roles_count > 0 ? '' : 'disabled' }}" data-toggle="tab" href="#tab-roles" role="tab">{{ trans_choice('app.search_tab_roles', $roles_count, ['count' => $roles_count . ($roles_count == 15 ? '+' : '')]) }}</a>
                </li>
                @endif

                @if ($list_time_entries_self)
                <li class="nav-item">
                    <a class="nav-link {{ $current_tab == 4 ? 'active' : '' }} {{ $time_entries_count > 0 ? '' : 'disabled' }}" data-toggle="tab" href="#tab-entries" role="tab">{{ trans_choice('app.search_tab_timeentries', $time_entries_count, ['count' => $time_entries_count . ($time_entries_count == 15 ? '+' : '')]) }}</a>
                </li>
                @endif

                @if ($list_users)
                <li class="nav-item">
                    <a class="nav-link {{ $current_tab == 5 ? 'active' : '' }} {{ $users_count > 0 ? '' : 'disabled' }}" data-toggle="tab" href="#tab-users" role="tab">{{ trans_choice('app.search_tab_users', $users_count, ['count' => $users_count . ($users_count == 15 ? '+' : '')]) }}</a>
                </li>
                @endif

                @if ($list_work_types_self)
                <li class="nav-item">
                    <a class="nav-link {{ $current_tab == 6 ? 'active' : '' }} {{ $work_types_count > 0 ? '' : 'disabled' }}" data-toggle="tab" href="#tab-worktypes" role="tab">{{ trans_choice('app.search_tab_worktypes', $work_types_count, ['count' => $work_types_count . ($work_types_count == 15 ? '+' : '')]) }}</a>
                </li>
                @endif
            </ul>
        </div>

        <div class="tab-content py-0">
            @if (empty($current_query))
            <div class="tab-pane fade active show" id="tab-0" role="tabpanel">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">{{ __('app.search_tab_noquery_message') }}</h4>
                </div>
            </div>
            @elseif ($current_tab == false)
            <div class="tab-pane fade active show" id="tab-0" role="tabpanel">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">{{ __('app.search_tab_noresults_message') }}</h4>
                </div>
            </div>
            @endif

            @if ($list_locations_self && $locations_count > 0)
            <div class="tab-pane fade {{ $current_tab == 1 ? 'active show' : '' }}" id="tab-locations" role="tabpanel">
                <div class="listview listview--bordered listview--hover">
                    @foreach ($locations as $location)
                    <a href="{{ route('user-locations.show', $location->id) }}" class="listview__item">
                        <i class="avatar-char bg-primary">{{ substr($location->name, 0, 2) }}</i>

                        <div class="listview__content">
                            @if ($show_locations_self)
                            <div class="listview__heading text-truncate">{{ $location->name }}</div>
                            <p class="listview__text">
                                @if ($show_locations_others)
                                <span class="mr-2"><i class="zmdi zmdi-account zmdi-hc-fw"></i> {{ $location->user->name }}</span>
                                @endif
                                <span class="mr-2"><i class="zmdi zmdi-car zmdi-hc-fw"></i> {{ __('app.global_distance_format', ['distance' => $location->distance]) }}</span>
                            </p>
                            @else
                            <div class="listview__heading text-truncate">{{ $location->name }}</div>
                            <p class="listview__text">
                                <span class="mr-2"><i class="zmdi zmdi-eye-off zmdi-hc-fw"></i></span>
                            </p>
                            @endif
                        </div>
                    </a>
                    @endforeach

                    @if ($locations_count == 15)
                    <div class="listview__item">
                        <div class="listview__content">
                            <p>{{ __('app.search_tab_moreresults_message') }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="clearfix mb-3"></div>
                </div>
            </div>
            @endif

            @if ($list_projects && $projects_count > 0)
            <div class="tab-pane fade {{ $current_tab == 2 ? 'active show' : '' }}" id="tab-projects" role="tabpanel">
                <div class="listview listview--bordered listview--hover">
                    @foreach ($projects as $project)
                    <a href="{{ route('projects.show', $project->id) }}" class="listview__item">
                        <i class="avatar-char bg-primary">{{ substr($project->name, 0, 2) }}</i>

                        <div class="listview__content">
                            @if ($show_projects)
                            <div class="listview__heading text-truncate">{{ $project->name }}</div>
                            <p class="listview__text">
                                @if ($show_money)
                                @if ($project->total_wage > -1)
                                <span class="mr-2"><i class="zmdi zmdi-balance zmdi-hc-fw"></i> {{ __('app.global_money_format', ['rate' => number_format($project->total_wage, 2)]) }}</span>
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
                    </a>
                    @endforeach

                    @if ($projects_count == 15)
                    <div class="listview__item">
                        <div class="listview__content">
                            <p>{{ __('app.search_tab_moreresults_message') }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="clearfix mb-3"></div>
                </div>
            </div>
            @endif

            @if ($list_roles && $roles_count > 0)
            <div class="tab-pane fade {{ $current_tab == 3 ? 'active show' : '' }}" id="tab-roles" role="tabpanel">
                <div class="listview listview--bordered listview--hover">
                    @foreach ($roles as $role)
                    <a href="{{ route('roles.show', $role->id) }}" class="listview__item">
                        <i class="avatar-char bg-primary">{{ substr($role->name, 0, 2) }}</i>

                        <div class="listview__content">
                            <div class="listview__heading text-truncate">{{ $role->name }}</div>
                            <p class="listview__text">
                                {{ trans_choice('app.search_item_usercount', $role->user_count, ['users' => $role->user_count]) }}
                            </p>
                        </div>
                    </a>
                    @endforeach

                    @if ($roles_count == 15)
                    <div class="listview__item">
                        <div class="listview__content">
                            <p>{{ __('app.search_tab_moreresults_message') }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="clearfix mb-3"></div>
                </div>
            </div>
            @endif

            @if ($list_time_entries_self && $time_entries_count > 0)
            <div class="tab-pane fade {{ $current_tab == 4 ? 'active show' : '' }}" id="tab-entries" role="tabpanel">
                <div class="listview listview--bordered listview--hover">
                    @foreach ($time_entries as $time_entry)
                    <a href="{{ route('time-entries.show', $time_entry->id) }}" class="listview__item">
                        <i class="avatar-char bg-primary">{{ $time_entry->project != null ? substr($time_entry->project->name, 0, 2) : '?' }}</i>

                        <div class="listview__content">
                            @if ($show_time_entries_self)
                            <div class="listview__heading text-truncate">{{ $time_entry->project != null ? $time_entry->project->name : __('app.global_empty_short') }} &mdash; {{ $time_entry->work_type != null ? $time_entry->work_type->name : __('app.global_empty_short') }}</div>
                            <p class="listview__text">
                                @if ($show_work_types_others)
                                <span class="mr-2"><i class="zmdi zmdi-account zmdi-hc-fw"></i> {{ $time_entry->user->name }}</span>
                                @endif
                                @if ($show_work_types_self)
                                @if ($time_entry->total_wage > -1)
                                <span class="mr-2"><i class="zmdi zmdi-balance zmdi-hc-fw"></i> {{ __('app.global_money_format', ['rate' => number_format($time_entry->total_wage, 2)]) }}</span>
                                @endif
                                @endif
                                @if ($time_entry->is_timer)
                                <span class="mr-2"><i class="zmdi zmdi-timer zmdi-hc-fw"></i> {{ $time_entry->start_time->format(__('app.global_time_format')) }}</span>
                                @else
                                <span class="mr-2"><i class="zmdi zmdi-time zmdi-hc-fw"></i> {{ $time_entry->start_time->format(__('app.global_time_format')) }} &mdash; {{ $time_entry->end_time->format(__('app.global_time_format')) }}</span>
                                @endif
                                <span class="mr-2"><i class="zmdi zmdi-time-interval zmdi-hc-fw"></i> {{ secondsToHms($time_entry->time_worked) }}</span>
                            </p>
                            @else
                            <div class="listview__heading text-truncate">{{ $time_entry->project != null ? $time_entry->project->name : __('app.global_empty_short') }} &mdash; {{ $time_entry->work_type != null ? $time_entry->work_type->name : __('app.global_empty_short') }}</div>
                            <p class="listview__text">
                                <span class="mr-2"><i class="zmdi zmdi-eye-off zmdi-hc-fw"></i></span>
                            </p>
                            @endif
                        </div>
                    </a>
                    @endforeach

                    @if ($time_entries_count == 15)
                    <div class="listview__item">
                        <div class="listview__content">
                            <p>{{ __('app.search_tab_moreresults_message') }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="clearfix mb-3"></div>
                </div>
            </div>
            @endif

            @if ($list_users && $users_count > 0)
            <div class="tab-pane fade {{ $current_tab == 5 ? 'active show' : '' }}" id="tab-users" role="tabpanel">
                <div class="listview listview--bordered listview--hover">
                    @foreach ($users as $user)
                    <a href="{{ route('users.show', $user->id) }}" class="listview__item">
                        <img class="listview__img" src="https://www.gravatar.com/avatar/{!! md5(strtolower(trim($user->email))) !!}?s=150&default=mp" alt="{{ $user->name }}">

                        <div class="listview__content">
                            <div class="listview__heading text-truncate">
                                {{ $user->name }}
                            </div>
                            <p class="listview__text">
                                @if ($user->is_active)
                                {{ $user->roles->first()->name }}
                                @else
                                {{ __('app.search_user_inactive_label') }}
                                @endif
                            </p>
                        </div>
                    </a>
                    @endforeach

                    @if ($users_count == 15)
                    <div class="listview__item">
                        <div class="listview__content">
                            <p>{{ __('app.search_tab_moreresults_message') }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="clearfix mb-3"></div>
                </div>
            </div>
            @endif

            @if ($list_work_types_self && $work_types_count > 0)
            <div class="tab-pane fade {{ $current_tab == 6 ? 'active show' : '' }}" id="tab-worktypes" role="tabpanel">
                <div class="listview listview--bordered listview--hover">
                    @foreach ($work_types as $work_type)
                    <a href="{{ route('work-types.show', $work_type->id) }}" class="listview__item">
                        <i class="avatar-char bg-primary">{{ substr($work_type->name, 0, 2) }}</i>

                        <div class="listview__content">
                            @if ($show_work_types_self)
                            <div class="listview__heading text-truncate">{{ $work_type->name }}</div>
                            <p class="listview__text">
                                @if ($show_work_types_others)
                                @if ($work_type->role instanceof \Spatie\Permission\Models\Role)
                                <span class="mr-2"><i class="zmdi zmdi-accounts zmdi-hc-fw"></i> {{ $work_type->role->name }}</span>
                                @else
                                <span class="mr-2"><i class="zmdi zmdi-accounts zmdi-hc-fw"></i> {{ __('app.search_role_empty') }}</span>
                                @endif
                                @endif
                                @if ('show_money')
                                @if ($work_type->total_wage > -1)
                                <span class="mr-2"><i class="zmdi zmdi-balance zmdi-hc-fw"></i> {{ __('app.global_money_format', ['rate' => number_format($work_type->total_wage, 2)]) }}</span>
                                @endif
                                @endif
                                <span class="mr-2"><i class="zmdi zmdi-time-interval zmdi-hc-fw"></i> {{ secondsToHms($work_type->time_worked) }}</span>
                            </p>
                            @else
                            <div class="listview__heading text-truncate">{{ $work_type->name }}</div>
                            <p class="listview__text">
                                <span class="mr-2"><i class="zmdi zmdi-eye-off zmdi-hc-fw"></i></span>
                            </p>
                            @endif
                        </div>
                    </a>
                    @endforeach

                    @if ($work_types_count == 15)
                    <div class="listview__item">
                        <div class="listview__content">
                            <p>{{ __('app.search_tab_moreresults_message') }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="clearfix mb-3"></div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
