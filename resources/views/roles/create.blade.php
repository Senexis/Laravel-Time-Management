@extends('layouts.app')

@section('title', __('app.menu_title_roles'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_roles') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.roles_create_card_title') }}</h4>

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('app.roles_create_title_label') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <i class="form-group__bar"></i>
            </div>

            @if ( count($permissions) > 0 )
            @foreach ($permissions as $category)
            <div class="form-group">
                <label>{{ __('app.roles_create_permissions_category_' . $category['name']) }}</label>
                <div class="row mb-4">
                    @foreach ($category['values'] as $permission)
                    @if (strpos(__('permissions.' . $permission), 'permissions.') === false)
                    <div class="col-sm-3 mb-2">
                        <div class="checkbox">
                            <input type="checkbox" name="permissions_{{ $permission }}" id="permissions_{{ $permission }}" value="true">
                            <label class="checkbox__label" for="permissions_{{ $permission }}">{{ __('permissions.' . $permission) }}</label>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="form-group">
                <button type="button" id="btn-check-all" class="btn btn-light btn--icon-text"><i class="zmdi zmdi-check-all zmdi-hc-fw"></i> {{ __('app.roles_create_permissions_checkallbutton') }}</button>
                <button type="button" id="btn-check-none" class="btn btn-light btn--icon-text"><i class="zmdi zmdi-format-clear-all zmdi-hc-fw"></i> {{ __('app.roles_create_permissions_checknonebutton') }}</button>
            </div>
            @endif

            <button type="submit" class="btn btn-primary">{{ __('app.roles_create_save_button') }}</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $("#btn-check-all").click(function() {
        $(":input[name^='permissions_']").prop('checked', 1)
    });
    $("#btn-check-none").click(function() {
        $(":input[name^='permissions_']").prop('checked', 0)
    });
</script>
@endpush
