@extends('layouts.auth')

@section('title', __('app.auth_title_email'))

@section('content')
<div class="login__block active">
    @if (session('status') || $errors->any())
    <div class="alert alert-primary" role="alert">
        {{ session('status') ?? $errors->first() }}
    </div>
    @endif

    <div class="login__block__body">
        <p class="mb-5">{{ __('app.auth_body_email') }}</p>

        <form role="form" method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group form-group--float form-group--centered mb-5">
                <input type="email" class="form-control" name="email" value="">
                <label>{{ __('app.auth_email') }}</label>
                <i class="form-group__bar"></i>
            </div>

            <div class="form-group form-group--centered">
                <a href="{{ route('login') }}" class="btn btn-light btn--icon" style="line-height:36px"><i class="zmdi zmdi-long-arrow-return"></i></a>
                <button type="submit" class="btn btn-primary btn--icon"><i class="zmdi zmdi-check"></i></button>
            </div>
        </form>
    </div>
</div>
@endsection
