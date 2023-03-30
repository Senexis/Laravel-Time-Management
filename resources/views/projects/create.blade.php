@extends('layouts.app')

@section('title', __('app.menu_title_projects'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_projects') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.projects_create_card_title') }}</h4>

        <form action="{{ route('projects.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('app.projects_create_name_label') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <i class="form-group__bar"></i>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('app.projects_create_save_button') }}</button>
        </form>
    </div>
</div>
@endsection
