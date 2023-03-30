@extends('layouts.auth')

@section('title', __('errors.423_title'))

@section('content')
<div class="error__inner">
    <h1>423</h1>
    <div class="card">
        <div class="card-body">
            <h2 class="mb-4">{{ __('errors.423_title') }}</h2>
            <p class="mb-5">{{ __('errors.423_body') }}</p>
            <a id="logout-link" href="#" class="btn btn-primary btn--raised">{{ __('errors.logout_button') }}</a>
        </div>
    </div>
</div>

<form class="d-none" method="POST" id="logout" action="{{ route('auth.logout') }}">
    @csrf
</form>
@endsection

@push('scripts')
<script>
    $('#logout-link').click(function(e) {
        e.preventDefault();
        $('#logout').submit();
    })
</script>
@endpush
