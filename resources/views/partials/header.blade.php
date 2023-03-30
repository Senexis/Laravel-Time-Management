<div class="navigation-trigger hidden-xl-up" data-ma-action="aside-open" data-ma-target=".sidebar">
    <div class="navigation-trigger__inner">
        <i class="navigation-trigger__line"></i>
        <i class="navigation-trigger__line"></i>
        <i class="navigation-trigger__line"></i>
    </div>
</div>

<div class="header__logo">
    <h1><a href="{{ url('/') }}">{{ config('app.name') }}</a></h1>
</div>

<form method="GET" action="{{ route('search.index') }}" class="search">
    <div class="search__inner">
        <input id="q" name="q" type="text" class="search__text" placeholder="{{ __('app.layout_search_placeholder') }}" autocapitalize="off" autocomplete="off" autocorrect="off" spellcheck="false">
        <i class="zmdi zmdi-search search__helper" data-ma-action="search-close"></i>
    </div>
</form>

<ul class="top-nav">
    @if ($stop_time_entries_self)
    <li class="hidden-xl-up"><a href="" data-ma-action="search-open"><i class="zmdi zmdi-search"></i></a></li>

    @if ($running_timer != false)
    <li id="top-nav-timer" class="mx-2">
        <i class="zmdi zmdi-timer zmdi-hc-fw"></i> <span id="top-nav-timer-text">{{ secondsToHmsAlternative($running_timer->time_worked) }}</span>
    </li>
    @if ($running_timer->is_running)
    <li>
        <a href="{{ route('time-entries.pause', $running_timer->id) }}" data-toggle="tooltip" title="{{ __('app.layout_timer_pause_button') }}">
            <i class="zmdi zmdi-pause zmdi-hc-fw"></i>
        </a>
    </li>
    @else
    <li>
        <a href="{{ route('time-entries.resume', $running_timer->id) }}" data-toggle="tooltip" title="{{ __('app.layout_timer_resume_button') }}">
            <i class="zmdi zmdi-play zmdi-hc-fw"></i>
        </a>
    </li>
    @endif
    <li>
        <a href="{{ route('time-entries.stop', $running_timer->id) }}" data-toggle="tooltip" title="{{ __('app.layout_timer_stop_button') }}">
            <i class="zmdi zmdi-stop zmdi-hc-fw"></i>
        </a>
    </li>
    @else
    <li>
        <a href="{{ route('time-entries.create') }}" data-toggle="tooltip" title="{{ __('app.layout_timer_create_button') }}">
            <i class="zmdi zmdi-timer zmdi-hc-fw"></i>
        </a>
    </li>
    @endif
    @endif

    @if ($send_feedback)
    <li data-toggle="tooltip" title="{{ __('app.layout_feedback_button') }}">
        <a  data-toggle="modal" data-target="#modal-feedback">
            <i class="zmdi zmdi-mood zmdi-hc-fw"></i>
        </a>
    </li>
    @endif
</ul>

@if ($stop_time_entries_self && $running_timer != false)
<script>
    var seconds = {{ $running_timer->time_worked }};

    function formatTime(totalSeconds) {
        var sec_num = parseInt(totalSeconds, 10);
        var hours = Math.floor(sec_num / 3600);
        var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
        var seconds = sec_num - (hours * 3600) - (minutes * 60);

        if (hours < 10) {
            hours = '0' + hours;
        }

        if (minutes < 10) {
            minutes = '0' + minutes;
        }

        if (seconds < 10) {
            seconds = '0' + seconds;
        }

        return hours + ':' + minutes + ':' + seconds;
    }

    document.getElementById('top-nav-timer').style.display = 'inline-block';
    document.getElementById('top-nav-timer-text').innerHTML = formatTime(seconds);

    @if($running_timer->is_running)
    setInterval(() => {
        seconds++;

        document.getElementById('top-nav-timer').style.display = 'inline-block';
        document.getElementById('top-nav-timer-text').innerHTML = formatTime(seconds);
    }, 1000);
    @endif
</script>
@endif
