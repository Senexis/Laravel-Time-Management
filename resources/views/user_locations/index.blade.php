@extends('layouts.app')

@section('title', __('app.menu_title_locations'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_locations') }}</h1>
</header>

<div class="row">
    <div class="col-lg-9">
        <div class="card">
            <div class="toolbar toolbar--inner">
                @if ($locations instanceof \Illuminate\Pagination\AbstractPaginator)
                <div class="toolbar__label">{{ trans_choice('app.locations_list_toolbar_label', $locations->total(), ['count' => $locations->count(), 'total' => $locations->total()]) }}</div>
                @else
                <div class="toolbar__label">{{ trans_choice('app.locations_list_toolbar_label', count($locations), ['count' => count($locations), 'total' => count($locations)]) }}</div>
                @endif

                <div class="actions">
                    @if ($create_locations)
                    <a href="{{ route('user-locations.create') }}" class="actions__item zmdi zmdi-plus zmdi-hc-fw" data-toggle="tooltip" title="{{ __('app.locations_list_card_link_create') }}"></a>
                    @endif
                </div>
            </div>

            <div class="listview listview--bordered listview--hover">
                @forelse($locations as $location)
                <div class="listview__item">
                    <i class="avatar-char bg-primary">{{ substr($location->name, 0, 2) }}</i>

                    <div class="listview__content">
                        @if ($show_locations_self)
                        <div class="listview__heading text-truncate"><a href="{{ route('user-locations.show', $location->id) }}">{{ $location->name }}</a></div>
                        <p class="listview__text">
                            @if ($show_locations_others)
                            <span class="mr-2"><a href="{{ route('users.show', $location->user->id) }}"><i class="zmdi zmdi-account zmdi-hc-fw"></i> {{ $location->user->name }}</a></span>
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
                    <div class="actions listview__actions">
                        @if ($edit_locations_self)
                        <a href="{{ route('user-locations.edit', $location->id) }}" class="actions__item zmdi zmdi-edit zmdi-hc-fw" data-toggle="tooltip" title="{{ __('app.locations_list_table_edit_button') }}"></a>
                        @endif
                        @if ($show_locations_self || $delete_locations_self)
                        <div class="dropdown actions__item">
                            <i class="actions__item zmdi zmdi-more-vert zmdi-hc-fw" data-toggle="dropdown"></i>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu--icon">
                                @if ($show_locations_self)
                                <a href="{{ route('user-locations.show', $location->id) }}" class="dropdown-item"><i class="zmdi zmdi-eye zmdi-hc-fw"></i> {{ __('app.locations_list_table_view_button') }}</a>
                                @endif
                                @if ($delete_locations_self)
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" data-toggle="modal" data-target="#modal-confirm-delete-{{ $location->id }}"><i class="zmdi zmdi-delete zmdi-hc-fw"></i> {{ __('app.locations_list_table_delete_button') }}</a>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if ($delete_locations_self)
                        <div class="modal fade" id="modal-confirm-delete-{{ $location->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title pull-left">{{ __('app.locations_list_modal_delete_title') }}</h5>
                                    </div>
                                    <div class="modal-body">{{ __('app.locations_list_modal_delete_body') }}</div>
                                    <div class="modal-footer">
                                        <form action="{{ route('user-locations.destroy', $location->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')

                                            <input type="submit" value="{{ __('app.locations_list_table_delete_button') }}" class="btn btn-danger text-black text-uppercase">
                                        </form>
                                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.locations_list_modal_close_button') }}</button>
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
                        <div class="listview__heading">{{ __('app.locations_list_table_noresults_title') }}</div>
                        <p>{{ __('app.locations_list_table_noresults_body') }}</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        @if ($locations instanceof \Illuminate\Pagination\AbstractPaginator)
        {{ $locations->appends(request()->except('page'))->onEachSide(2)->links() }}
        @endif
    </div>
    <div class="col-lg-3 mt-4 mt-lg-0">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ __('app.locations_list_modal_filters_title') }}</h4>
                <form action="" method="get">
                    <div class="form-group">
                        <label for="amount">{{ __('app.locations_list_modal_filters_amount_label') }}</label>
                        <div class="select">
                            <select name="amount" id="amount" class="form-control select2">
                                <option value="">{{ __('app.global_select_prepend') }}</option>
                                <option value="15" {{ $current_amount == '15' ? 'selected="selected"' : '' }}>{{ __('app.locations_list_modal_filters_amount15_option') }}</option>
                                <option value="50" {{ $current_amount == '50' ? 'selected="selected"' : '' }}>{{ __('app.locations_list_modal_filters_amount50_option') }}</option>
                                <option value="100" {{ $current_amount == '100' ? 'selected="selected"' : '' }}>{{ __('app.locations_list_modal_filters_amount100_option') }}</option>
                                <option value="250" {{ $current_amount == '250' ? 'selected="selected"' : '' }}>{{ __('app.locations_list_modal_filters_amount250_option') }}</option>
                                <option value="-1" {{ $current_amount == '-1' ? 'selected="selected"' : '' }}>{{ __('app.locations_list_modal_filters_amountall_option') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="from_time">{{ __('app.locations_list_modal_filters_fromtime_label') }}</label>
                        <input type="text" name="from_time" id="from_time" class="form-control datetime-picker-custom" value="{{ $current_from_time }}" placeholder="{{ __('app.global_select_prepend') }}">
                        <i class="form-group__bar"></i>
                    </div>

                    <div class="form-group">
                        <label for="to_time">{{ __('app.locations_list_modal_filters_totime_label') }}</label>
                        <input type="text" name="to_time" id="to_time" class="form-control datetime-picker-custom" value="{{ $current_to_time }}" placeholder="{{ __('app.global_select_prepend') }}">
                        <i class="form-group__bar"></i>
                    </div>

                    <div class="form-group">
                        <label for="type">{{ __('app.locations_list_modal_filters_type_label') }}</label>
                        <div class="select">
                            <select name="type" id="type" class="form-control select2">
                                <option value="">{{ __('app.global_select_prepend') }}</option>
                                <option value="used" {{ $current_type == 'used' ? 'selected="selected"' : '' }}>{{ __('app.locations_list_modal_filters_used_option') }}</option>
                                <option value="unused" {{ $current_type == 'unused' ? 'selected="selected"' : '' }}>{{ __('app.locations_list_modal_filters_unused_option') }}</option>
                            </select>
                        </div>
                    </div>

                    @if ($show_locations_others)
                    <div class="form-group">
                        <label for="user_id">{{ __('app.locations_list_modal_filters_userid_label') }}</label>
                        <div class="select">
                            <select name="user_id" id="user_id" class="form-control select2">
                                @foreach ($users_select as $key => $user)
                                <option value="{{ $key }}" {{ $key == $current_user ? 'selected="selected"' : '' }}>{{ $user }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif

                    <a href="{{ route('user-locations.index') }}" class="btn btn-outline-secondary btn-block">{{ __('app.locations_list_modal_filters_clear_button') }}</a>
                    <input type="submit" class="btn btn-outline-primary btn-block" value="{{ __('app.locations_list_modal_apply_button') }}">
                </form>
            </div>
        </div>
    </div>
</div>

@if ($create_locations)
<a href="{{ route('user-locations.create') }}" class="btn btn-primary btn--action zmdi zmdi-plus zmdi-hc-fw"></a>
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
