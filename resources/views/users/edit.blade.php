@extends('layouts.app')

@section('title', __('app.menu_title_users'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_users') }}</h1>
</header>

@if ($user->id == Auth::user()->id)
<div class="alert alert-warning text-black" role="alert">
    <p class="mb-0"><i class="zmdi zmdi-alert-triangle zmdi-hc-fw"></i> {{ __('app.users_edit_roles_disabledself') }}</p>
</div>
@endif

<div class="card new-contact">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.users_edit_card_title') }}</h4>

        <form action="{{ route('users.update', $user->id) }}" method="post">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">{{ __('app.users_edit_name_label') }}</label>
                        <input type="text" name="name" id="name" value="{{ $user->name }}" class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <i class="form-group__bar"></i>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">{{ __('app.users_edit_password_label') }}</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <i class="form-group__bar"></i>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">{{ __('app.users_edit_email_label') }}</label>
                        <input type="email" name="email" id="email" value="{{ $user->email }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <i class="form-group__bar"></i>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role_id">{{ __('app.users_edit_role_label') }}</label>
                        @if ($user->id == Auth::user()->id)
                        <div class="select">
                            <select name="role_id" id="role_id" class="form-control select2" disabled="disabled">
                                @foreach ($roles as $key => $value)
                                <option value="{{ $key }}" {{ $key == $user->roles[0]->id ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="select">
                            <select name="role_id" id="role_id" class="form-control select2">
                                @foreach ($roles as $key => $value)
                                <option value="{{ $key }}" {{ $key == $user->roles[0]->id ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="hourly_rate">{{ __('app.users_edit_hourlyrate_label') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ __('app.users_edit_hourlyrate_prefix') }}</span>
                            </div>
                            <input type="text" name="hourly_rate" id="hourly_rate" value="{{ $user->hourly_rate }}" class="money form-control @error('hourly_rate') is-invalid @enderror">
                            @error('hourly_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <i class="form-group__bar"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="travel_expenses">{{ __('app.users_edit_travelexpenses_label') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ __('app.users_edit_travelexpenses_prefix') }}</span>
                            </div>
                            <input type="text" name="travel_expenses" id="travel_expenses" value="{{ $user->travel_expenses }}" class="money form-control @error('travel_expenses') is-invalid @enderror">
                            @error('travel_expenses')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <i class="form-group__bar"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="locale">{{ __('app.users_edit_locale_label') }}</label>
                        <div class="select">
                            <select name="locale" id="locale" class="form-control select2">
                                @foreach ($locales as $key => $value)
                                <option value="{{ $key }}" {{ $key == $user->locale ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="timezone">{{ __('app.users_edit_timezone_label') }}</label>
                        <div class="select">
                            <select name="timezone" id="timezone" class="form-control select2">
                                @foreach ($timezones as $key => $value)
                                <option value="{{ $key }}" {{ $key == $user->timezone ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="timeular_id">{{ __('app.users_edit_timeular_label') }}</label>
                        <input type="number" name="timeular_id" id="timeular_id" value="{{ $user->timeular_id }}" class="form-control @error('timeular_id') is-invalid @enderror">
                        @error('timeular_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <i class="form-group__bar"></i>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <div class="checkbox">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} {{ $user->id == Auth::user()->id ? 'disabled' : '' }}>
                            <label class="checkbox__label" for="is_active">{{ __('app.users_edit_isactive_label') }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="mt-5 text-center">
                <button type="submit" class="btn btn-primary">{{ __('app.users_edit_save_button') }}</button>
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