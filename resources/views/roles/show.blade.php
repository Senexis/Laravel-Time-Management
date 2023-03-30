@extends('layouts.app')

@section('title', __('app.menu_title_roles'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_roles') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.roles_show_card_title') }}</h4>

        <dl class="row">
            <dt class="col-sm-3 text-truncate">{{ __('app.roles_show_name_label') }}</dt>
            <dd class="col-sm-9">{{ $role->name }}</dd>

            <dt class="col-sm-3 text-truncate">{{ __('app.roles_show_users_label') }}</dt>
            <dd class="col-sm-9">
                @if ( count($role->users) > 0 )
                <ul class="list-unstyled">
                    @foreach ($role->users as $user)
                    <li><a href="{{ route('users.show', $user->id) }}">{{ $user->name }}</a></li>
                    @endforeach
                </ul>
                @else
                {{ __('app.roles_show_hasnousers_label') }}
                @endif
            </dd>
        </dl>

        @if ( count($permissions) > 0 )
        @foreach ($permissions as $category)
        <div class="permission-group">
            <label>{{ __('app.roles_show_permissions_category_' . $category['name']) }}</label>
            <div class="row mb-4">
                @foreach ($category['values'] as $permission)
                @if (strpos(__('permissions.' . $permission), 'permissions.') === false)
                <div class="col-sm-3 mb-2 text-truncate">
                    @if ( in_array($permission, $permissions_owned) )
                    <span><i class="zmdi zmdi-check-square"></i> <span>{{ __('permissions.' . $permission) }}</span></span>
                    @else
                    <span><i class="zmdi zmdi-square-o"></i> <span>{{ __('permissions.' . $permission) }}</span></span>
                    @endif
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endforeach
        @endif

        <hr class="mt-3 mb-4">

        <a href="{{ route('roles.index') }}" class="btn btn-primary">{{ __('app.roles_show_card_link_back') }}</a>
        @can('edit.roles')
        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary">{{ __('app.roles_show_card_link_edit') }}</a>
        @endcan
        @can('delete.roles')
        <a class="btn btn-danger text-black" data-toggle="modal" data-target="#modal-confirm-delete">{{ __('app.roles_show_card_link_delete') }}</a>
        @endcan
    </div>
</div>

@can('delete.roles')
<div class="modal fade" id="modal-confirm-delete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pull-left">{{ __('app.roles_show_modal_delete_title') }}</h5>
            </div>
            <div class="modal-body">{{ __('app.roles_show_modal_delete_body') }}</div>
            <div class="modal-footer">
                <form action="{{ route('roles.destroy', $role->id) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <input type="submit" value="{{ __('app.roles_show_table_delete_button') }}" class="btn btn-danger text-black text-uppercase">
                </form>
                <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.roles_show_modal_close_button') }}</button>
            </div>
        </div>
    </div>
</div>
@endcan

@endsection
