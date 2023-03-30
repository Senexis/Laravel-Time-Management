@extends('layouts.app')

@section('title', __('app.menu_title_dashboard'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_dashboard') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.home_card_title') }}</h4>
        <p class="lead">{{ __('app.home_card_lead') }}</p>
    </div>
</div>
@endsection
