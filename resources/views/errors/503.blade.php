@extends('layouts.auth')

@section('title', __('errors.503_title'))

@section('content')
<div class="error__inner">
    <h1>503</h1>
    <div class="card">
        <div class="card-body">
            <h2 class="mb-4">{{ __('errors.503_title') }}</h2>
            <p class="mb-0">{{ __('errors.503_body') }}</p>
        </div>
    </div>
</div>
@endsection
