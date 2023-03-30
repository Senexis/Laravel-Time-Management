@extends('layouts.auth')

@section('title', __('errors.' . $exception_status_code . '_title'))

@section('content')
<div class="error__inner">
    <h1>{{ $exception_status_code }}</h1>
    <div class="card">
        <div class="card-body">
            <h2 class="mb-4">{{ __('errors.' . $exception_status_code . '_title') }}</h2>
            <p class="mb-5">{{ __('errors.' . $exception_status_code . '_body') }}</p>
            <a href="{{ url('/') }}" class="btn btn-primary btn--raised">{{ __('errors.back_button') }}</a>
        </div>
    </div>
</div>
@endsection
