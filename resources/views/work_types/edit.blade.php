@extends('layouts.app')

@section('title', __('app.menu_title_worktypes'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_worktypes') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.worktypes_edit_card_title') }}</h4>

        <form action="{{ route('work-types.update', $work_type->id) }}" method="post">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">{{ __('app.worktypes_edit_name_label') }}</label>
                <input type="text" name="name" id="name" value="{{ $work_type->name }}" class="form-control @error('name') is-invalid @enderror">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <i class="form-group__bar"></i>
            </div>

            <div class="form-group">
                <label for="role_id">{{ __('app.worktypes_edit_role_label') }}</label>
                <div class="select">
                    <select name="role_id" id="role_id" class="form-control select2">
                        @foreach ($roles as $key => $value)
                        <option value="{{ $key }}" {{ $key == $work_type->role_id ? 'selected="selected"' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('app.worktypes_edit_save_button') }}</button>
        </form>
    </div>
</div>
@endsection
