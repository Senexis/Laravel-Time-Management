@extends('layouts.app')

@section('title', __('app.menu_title_roles'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_roles') }}</h1>
</header>

<div class="toolbar">
    @if ($roles instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="toolbar__label">{{ trans_choice('app.roles_list_toolbar_label', $roles->total(), ['count' => $roles->count(), 'total' => $roles->total()]) }}</div>
    @else
    <div class="toolbar__label">{{ trans_choice('app.roles_list_toolbar_label', count($roles), ['count' => count($roles), 'total' => count($roles)]) }}</div>
    @endif

    <div class="actions">
        @if ($create_roles)
        <a href="{{ route('roles.create') }}" class="actions__item zmdi zmdi-plus zmdi-hc-fw"></a>
        @endif
    </div>
</div>

<div class="row groups">
    @if (count($roles) > 0)
    @foreach ($roles as $role)
    <div class="col-lg-3 col-md-4 col-6">
        <div class="groups__item">
            <div class="groups__img">
                @foreach ($role->users as $user)
                <a href="{{ route('users.show', $user->id) }}">
                    <img class="avatar-img" src="https://www.gravatar.com/avatar/{!! md5(strtolower(trim($user->email))) !!}?s=150&default=mp" alt="{{ $user->name }}" data-toggle="tooltip" title="{{ $user->name }}">
                </a>
                @endforeach
                @if ($role->user_count < 4) @for ($i=$role->user_count; $i < 4; $i++) <div class="avatar-img avatar-char bg-light">
            </div>
            @endfor
            @endif
        </div>

        <div class="groups__info">
            <strong>{{ $role->name }}</strong>
            <small>{{ trans_choice('app.roles_list_item_usercount', $role->user_count, ['users' => $role->user_count]) }}</small>
        </div>

        @if ($show_roles || $edit_roles || $delete_roles)
        <div class="mt-4 text-centered">
            @if ($show_roles)
            <a href="{{ route('roles.show', $role->id) }}" class="btn btn-primary btn--icon"><i class="zmdi zmdi-eye zmdi-hc-fw" style="line-height: 38px;"></i></a>
            @endif
            @if ($edit_roles)
            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary btn--icon"><i class="zmdi zmdi-edit zmdi-hc-fw" style="line-height: 38px;"></i></a>
            @endif
            @if ($delete_roles)
            @if ($role->id != 1)
            <button class="btn btn-danger text-black btn--icon" data-toggle="modal" data-target="#modal-confirm-delete-{{ $role->id }}"><i class="zmdi zmdi-delete zmdi-hc-fw"></i></button>

            <div class="modal fade" id="modal-confirm-delete-{{ $role->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title pull-left">{{ __('app.roles_list_modal_delete_title', ["role" => $role->name]) }}</h5>
                        </div>
                        <div class="modal-body text-left">{{ __('app.roles_list_modal_delete_body', ["role" => $role->name]) }}</div>
                        <div class="modal-footer">
                            <form action="{{ route('roles.destroy', $role->id) }}" method="post">
                                @csrf
                                @method('DELETE')

                                <input type="submit" value="{{ __('app.roles_list_table_delete_button') }}" class="btn btn-danger text-black text-uppercase">
                            </form>
                            <button type="button" class="btn btn-light text-uppercase" data-dismiss="modal">{{ __('app.roles_list_modal_close_button') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <button class="btn btn-danger text-black btn--icon" disabled="disabled"><i class="zmdi zmdi-delete zmdi-hc-fw"></i></button>
            @endif
            @endif
        </div>
        @endif
    </div>
</div>
@endforeach
@else
<p>{{ __('app.roles_list_table_noentries_text') }}</p>
@endif
</div>

@if ($roles instanceof \Illuminate\Pagination\AbstractPaginator)
{{ $roles->appends(request()->except('page'))->onEachSide(2)->links() }}
@endif

@if ($create_roles)
<a href="{{ route('roles.create') }}" class="btn btn-primary btn--action zmdi zmdi-plus zmdi-hc-fw"></a>
@endif

@endsection