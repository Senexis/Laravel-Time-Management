@extends('layouts.app')

@section('title', __('app.menu_title_locations'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_locations') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.locations_create_card_title') }}</h4>

        <form action="{{ route('user-locations.store') }}" method="post">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('app.locations_create_name_label') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <i class="form-group__bar"></i>
            </div>

            <div class="form-group">
                <label for="distance">{{ __('app.locations_create_distance_label') }}</label>
                <div class="input-group">
                    <input type="text" name="distance" id="distance" value="{{ number_format(!empty(old('distance')) ? old('distance') : 0, 1) }}" class="form-control @error('distance') is-invalid @enderror">
                    @error('distance')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="input-group-append">
                        <span class="input-group-text">{{ __('app.locations_create_distance_suffix') }}</span>
                    </div>
                    <i class="form-group__bar"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('app.locations_create_save_button') }}</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#distance').mask("00000.0", {
            reverse: true
        });
    });
</script>
@endpush
