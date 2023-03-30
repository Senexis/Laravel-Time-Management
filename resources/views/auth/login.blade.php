@extends('layouts.auth')

@section('title', __('app.auth_title_login'))

@section('content')
<div class="login__block active">
    <div class="login__block__body">
        <p class="mb-5">{{ __('app.auth_body_login') }} <a href="{{ route('password.request') }}">{{ __('app.auth_body_login_link') }}</a></p>

        <form role="form" method="POST" action="{{ url('login') }}">
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
                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                <label>{{ __('app.auth_password') }}</label>
                <i class="form-group__bar"></i>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @endif
            </div>

            <div class="form-group form-group--centered">
                <div class="checkbox">
                    <input type="checkbox" id="remember" name="remember">
                    <label class="checkbox__label" for="remember">{{ __('app.auth_remember_me') }}</label>
                </div>
            </div>

            <div class="form-group form-group--centered">
                <button type="submit" class="btn btn-primary btn--icon"><i class="zmdi zmdi-check"></i></button>
            </div>
        </form>
    </div>
</div>
@endsection
