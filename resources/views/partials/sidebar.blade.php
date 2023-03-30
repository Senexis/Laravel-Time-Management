@inject('request', 'Illuminate\Http\Request')

<div class="user">
    <div class="user__info">
        <img class="user__img" src="https://www.gravatar.com/avatar/{!! md5(strtolower(trim(Auth::user()->email))) !!}?s=40&default=mp" alt="{{ Auth::user()->name }}" width="40" height="40">
        <div>
            <div class="user__name">{{ Auth::user()->name }}</div>
            <div class="user__email">{{ Auth::user()->email }}</div>
        </div>
    </div>
</div>

<ul class="navigation">
    <li class="{{ $request->segment(1) == '' ? 'navigation__active' : '' }}">
        <a href="{{ url('/') }}">
            <i class="zmdi zmdi-home"></i> {{ __('app.menu_title_dashboard') }}
        </a>
    </li>

    @can('list.time_entries.self')
    <li class="{{ $request->segment(1) == 'time-entries' ? 'navigation__active' : '' }}">
        <a href="{{ route('time-entries.index') }}">
            <i class="zmdi zmdi-time"></i> {{ __('app.menu_title_timeentries') }}
        </a>
    </li>
    @endcan

    @can('show.reports.self')
    <li class="navigation__sub @if ( $request->segment(1) == 'reports' ) navigation__sub--active navigation__sub--toggled @endif">
        <a href="{{ route('reports.user') }}"><i class="zmdi zmdi-trending-up"></i> {{ __('app.menu_title_reports') }}</a>

        <ul>
            <li class="{{ $request->segment(1) == 'reports' && $request->segment(2) == 'user' ? 'navigation__active' : '' }}">
                <a href="{{ route('reports.user') }}">{{ __('app.menu_title_reports_user') }}</a>
            </li>

            <li class="{{ $request->segment(1) == 'reports' && $request->segment(2) == 'project' ? 'navigation__active' : '' }}">
                <a href="{{ route('reports.project') }}">{{ __('app.menu_title_reports_project') }}</a>
            </li>

            <li class="{{ $request->segment(1) == 'reports' && $request->segment(2) == 'work-type' ? 'navigation__active' : '' }}">
                <a href="{{ route('reports.work-type') }}">{{ __('app.menu_title_reports_worktype') }}</a>
            </li>
        </ul>
    </li>
    @endcan

    @if ( Auth::user()->hasAnyPermission(['list.projects', 'list.work_types.self', 'list.locations.self']) )
    <li class="divider"></li>

    @can('list.projects')
    <li class="{{ $request->segment(1) == 'projects' ? 'navigation__active' : '' }}">
        <a href="{{ route('projects.index') }}"><i class="zmdi zmdi-developer-board"></i> {{ __('app.menu_title_projects') }}</a>
    </li>
    @endcan

    @can('list.work_types.self')
    <li class="{{ $request->segment(1) == 'work-types' ? 'navigation__active' : '' }}">
        <a href="{{ route('work-types.index') }}"><i class="zmdi zmdi-case"></i> {{ __('app.menu_title_worktypes') }}</a>
    </li>
    @endcan

    @can('list.locations.self')
    <li class="{{ $request->segment(1) == 'user-locations' ? 'navigation__active' : '' }}">
        <a href="{{ route('user-locations.index') }}"><i class="zmdi zmdi-pin"></i> {{ __('app.menu_title_locations') }}</a>
    </li>
    @endcan
    @endif

    @if (Auth::user()->hasAnyPermission(['list.users', 'list.roles', 'show.user_actions']))
    <li class="divider"></li>

    @can('list.users')
    <li class="{{ $request->segment(1) == 'users' ? 'navigation__active' : '' }}">
        <a href="{{ route('users.index') }}"><i class="zmdi zmdi-accounts"></i> {{ __('app.menu_title_users') }}</a>
    </li>
    @endcan

    @can('list.roles')
    <li class="{{ $request->segment(1) == 'roles' ? 'navigation__active' : '' }}">
        <a href="{{ route('roles.index') }}"><i class="zmdi zmdi-assignment-account"></i> {{ __('app.menu_title_roles') }}</a>
    </li>
    @endcan

    @can('show.user_actions')
    <li class="{{ $request->segment(1) == 'user-actions' ? 'navigation__active' : '' }}">
        <a href="{{ route('user-actions.index') }}"><i class="zmdi zmdi-book"></i> {{ __('app.menu_title_useractions') }}</a>
    </li>
    @endcan
    @endif

    <li class="divider"></li>

    <li>
        <a id="logout-link" href="#">
            <i class="zmdi zmdi-long-arrow-return"></i> {{ __('app.menu_title_logout') }}
        </a>
    </li>
</ul>

<form class="d-none" method="POST" id="logout" action="{{ route('auth.logout') }}">
    @csrf
</form>

@push('scripts')
<script>
    $('#logout-link').click(function(e) {
        e.preventDefault();
        $('#logout').submit();
    })
</script>
@endpush
