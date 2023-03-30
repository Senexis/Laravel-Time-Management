@extends('layouts.app')

@section('title', __('app.menu_title_locations'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_locations') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.locations_show_card_title') }}</h4>

        <dl class="row">
            <dt class="col-sm-3 text-truncate">{{ __('app.locations_show_name_label') }}</dt>
            <dd class="col-sm-9">{{ $location->name }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.locations_show_distance_label') }}</dt>
            <dd class="col-sm-9">{{ __('app.global_distance_format', ['distance' => $location->distance]) }}</dd>

            @can('show.money')
            <dt class="col-sm-3 text-truncate">{{ __('app.locations_show_ratepertrip_label') }}</dt>
            <dd class="col-sm-9">{{ __('app.global_money_format', ['rate' => number_format($location->trip_cost, 2)]) }}</dd>
            @endcan

            @can('show.locations.others')
            <dt class="col-sm-3 text-truncate">{{ __('app.locations_show_user_label') }}</dt>
            <dd class="col-sm-9"><a href="{{ route('users.show', $location->user->id) }}">{{ $location->user->name }}</a></dd>
            @endcan

            <dt class="col-sm-3 text-truncate">{{ __('app.locations_show_timesused_label') }}</dt>
            <dd class="col-sm-9">{{ number_format($location->time_entries->count()) }}</dd>
        </dl>

        <hr class="mt-3 mb-4">

        <a href="{{ route('user-locations.index') }}" class="btn btn-primary">{{ __('app.locations_show_card_link_back') }}</a>
        @can('edit.locations.self')
        <a href="{{ route('user-locations.edit', $location->id) }}" class="btn btn-primary">{{ __('app.locations_show_card_link_edit') }}</a>
        @endcan
        @can('delete.locations.self')
        <a class="btn btn-danger text-black" data-toggle="modal" data-target="#modal-confirm-delete">{{ __('app.locations_show_card_link_delete') }}</a>
        @endcan
    </div>
</div>

@can('delete.locations.self')
<div class="modal fade" id="modal-confirm-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pull-left">{{ __('app.locations_show_modal_delete_title') }}</h5>
            </div>
            <div class="modal-body">{{ __('app.locations_show_modal_delete_body') }}</div>
            <div class="modal-footer">
                <form action="{{ route('user-locations.destroy', $location->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <input type="submit" value="{{ __('app.locations_show_table_delete_button') }}" class="btn btn-danger text-black text-uppercase">
                </form>
                <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.locations_show_modal_close_button') }}</button>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection
