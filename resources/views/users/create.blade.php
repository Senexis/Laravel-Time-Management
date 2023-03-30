@extends('layouts.app')

@section('title', __('app.menu_title_users'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_users') }}</h1>
</header>

<div class="card new-contact">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.users_create_card_title') }}</h4>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">{{ __('app.users_create_name_label') }}</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <i class="form-group__bar"></i>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">{{ __('app.users_create_password_label') }}</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <i class="form-group__bar"></i>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">{{ __('app.users_create_email_label') }}</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <i class="form-group__bar"></i>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role_id">{{ __('app.users_create_role_label') }}</label>
                        <div class="select">
                            <select name="role_id" id="role_id" class="form-control select2">
                                @foreach ($roles as $key => $value)
                                <option value="{{ $key }}" {{ $key == old('role_id') ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="hourly_rate">{{ __('app.users_create_hourlyrate_label') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ __('app.users_create_hourlyrate_prefix') }}</span>
                            </div>
                            <input type="text" name="hourly_rate" id="hourly_rate" value="{{ old('hourly_rate') }}" class="money form-control @error('hourly_rate') is-invalid @enderror">
                            @error('hourly_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <i class="form-group__bar"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="travel_expenses">{{ __('app.users_create_travelexpenses_label') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ __('app.users_create_travelexpenses_prefix') }}</span>
                            </div>
                            <input type="text" name="travel_expenses" id="travel_expenses" value="{{ old('travel_expenses') }}" class="money form-control @error('travel_expenses') is-invalid @enderror">
                            @error('travel_expenses')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <i class="form-group__bar"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="locale">{{ __('app.users_create_locale_label') }}</label>
                        <div class="select">
                            <select name="locale" id="locale" class="form-control select2">
                                @foreach ($locales as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="timezone">{{ __('app.users_create_timezone_label') }}</label>
                        <div class="select">
                            <select name="timezone" id="timezone" class="form-control select2">
                                @foreach ($timezones as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="timeular_id">{{ __('app.users_create_timeular_label') }}</label>
                        <input type="number" name="timeular_id" id="timeular_id" value="{{ old('timeular_id') }}" class="form-control @error('timeular_id') is-invalid @enderror">
                        @error('timeular_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <i class="form-group__bar"></i>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="mt-5 text-center">
                <button type="submit" class="btn btn-primary">{{ __('app.users_create_save_button') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('.money').mask("0000.00", {
            reverse: true
        });
    });
</script>
@endpush