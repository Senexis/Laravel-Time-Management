@extends('layouts.app')

@section('title', __('app.menu_title_users'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_users') }}</h1>
</header>

<div class="toolbar">
    @if ($users instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="toolbar__label">{{ trans_choice('app.users_list_toolbar_label', $users->total(), ['count' => $users->count(), 'total' => $users->total()]) }}</div>
    @else
    <div class="toolbar__label">{{ trans_choice('app.users_list_toolbar_label', count($users), ['count' => count($users), 'total' => count($users)]) }}</div>
    @endif

    <div class="actions">
        @if ($create_users)
        <a href="{{ route('users.create') }}" class="actions__item zmdi zmdi-plus zmdi-hc-fw"></a>
        @endif
    </div>
</div>

<div class="contacts row align-items-center">
    @if (count($users) > 0)
    @foreach ($users as $user)
    <div class="col-lg-3 col-md-4 col-6">
        <div class="contacts__item {{ !$user->is_active ? 'bg-light' : '' }}">
            @if ($show_users)
            <a href="{{ route('users.show', $user->id) }}" class="contacts__img">
                <img src="https://www.gravatar.com/avatar/{!! md5(strtolower(trim($user->email))) !!}?s=150&default=mp" alt="{{ $user->name }}">
            </a>
            @else
            <span class="contacts__img">
                <img src="https://www.gravatar.com/avatar/{!! md5(strtolower(trim($user->email))) !!}?s=150&default=mp" alt="{{ $user->name }}">
            </span>
            @endif

            <div class="contacts__info">
                <strong>{{ $user->name }}</strong>
                @if ($user->is_active)
                <small>{{ $user->roles->first()->name }}</small>
                @else
                <small class="font-italic">{{ __('app.users_list_user_inactive_label') }}</small>
                @endif
            </div>

            @if ($show_users || $edit_users || $login_as_users || $delete_users)
            <div class="mt-4 text-centered">
                @if ($show_users)
                <a href="{{ route('users.show', $user->id) }}" class="btn btn-primary btn--icon"><i class="zmdi zmdi-eye zmdi-hc-fw" style="line-height: 38px;"></i></a>
                @endif
                @if ($edit_users)
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn--icon"><i class="zmdi zmdi-edit zmdi-hc-fw" style="line-height: 38px;"></i></a>
                @endif
                @if ($login_as_users)
                @if ($user->id != Auth::user()->id)
                <a href="{{ route('users.login-as', $user->id) }}" class="btn btn-warning text-black btn--icon"><i class="zmdi zmdi-sign-in zmdi-hc-fw" style="line-height: 38px;"></i></a>
                @else
                <button class="btn btn-warning text-black btn--icon" disabled="disabled"><i class="zmdi zmdi-sign-in zmdi-hc-fw"></i></button>
                @endif
                @endif
                @if ($delete_users)
                @if ($user->id != Auth::user()->id)
                <button class="btn btn-danger text-black btn--icon" data-toggle="modal" data-target="#modal-confirm-delete-{{ $user->id }}"><i class="zmdi zmdi-delete zmdi-hc-fw"></i></button>

                <div class="modal fade" id="modal-confirm-delete-{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title pull-left">{{ __('app.users_list_modal_delete_title', ["user" => $user->name]) }}</h5>
                            </div>
                            <div class="modal-body text-left">{{ __('app.users_list_modal_delete_body', ["user" => $user->name]) }}</div>
                            <div class="modal-footer">
                                <form action="{{ route('users.destroy', $user->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')

                                    <input type="submit" value="{{ __('app.users_list_table_delete_button') }}" class="btn btn-danger text-black text-uppercase">
                                </form>
                                <button type="button" class="btn btn-light text-uppercase" data-dismiss="modal">{{ __('app.users_list_modal_close_button') }}</button>
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
    @endif
</div>

@if ($users instanceof \Illuminate\Pagination\AbstractPaginator)
{{ $users->appends(request()->except('page'))->onEachSide(2)->links() }}
@endif

@if ($create_users)
<a href="{{ route('users.create') }}" class="btn btn-primary btn--action zmdi zmdi-plus zmdi-hc-fw"></a>
@endif

@endsection
