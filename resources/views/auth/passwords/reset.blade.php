@extends('layouts.auth')

@section('title', __('app.auth_title_reset'))

@section('content')
<div class="login__block active">
    <div class="login__block__body">
        <p class="mb-5">{{ __('app.auth_body_reset') }}</p>

        <form role="form" method="POST" action="{{ route('password.request') }}">
            @csrf

            <div class="form-group form-group--float form-group--centered mb-5">
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}">
                <label>{{ __('app.auth_email') }}</label>
                <i class="form-group__bar"></i>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @endif
            </div>

            <div class="form-group form-group--float form-group--centered mb-5">
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}">
                <label>{{ __('app.auth_password') }}</label>
                <i class="form-group__bar"></i>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @endif
            </div>

            <div class="form-group form-group--float form-group--centered mb-5">
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" value="{{ old('password_confirmation') }}">
                <label>{{ __('app.auth_password_confirm') }}</label>
                <i class="form-group__bar"></i>
                @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
                @endif
            </div>

            <div class="form-group form-group--centered">
                <button type="submit" class="btn btn-primary btn--icon"><i class="zmdi zmdi-check"></i></button>
            </div>
        </form>
    </div>
</div>
@endsection
