@extends('layouts.app')

@section('title', __('app.menu_title_timeentries'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_timeentries') }}</h1>
</header>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.timeentries_create_card_title') }}</h4>
        @can('start.time_entries')
        <h6 class="card-subtitle">{{ __('app.timeentries_create_card_subtitle') }}</h6>
        @endcan

        @if (count($locations) > 0)
        @can('start.time_entries')
        <div class="tab-container">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link @if(!$errors->any()) active @endif" data-toggle="tab" href="#timer" role="tab" aria-selected="false">{{ __('app.timeentries_create_tab_timer') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($errors->any()) active @endif" data-toggle="tab" href="#time_entry" role="tab" aria-selected="false">{{ __('app.timeentries_create_tab_timeentry') }}</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade @if(!$errors->any()) active show @endif" id="timer" role="tabpanel">
                    @if ($timers_count == 0)
                    <form action="{{ route('time-entries.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="is_timer" value="1">

                        <div class="form-group">
                            <label for="project_id">{{ __('app.timeentries_create_projectid_label') }}</label>
                            <div class="select">
                                <select name="project_id" id="project_id" class="form-control select2">
                                    @foreach ($projects as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $user->last_project ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="work_type_id">{{ __('app.timeentries_create_worktypeid_label') }}</label>
                            <div class="select">
                                <select name="work_type_id" id="work_type_id" class="form-control select2">
                                    @foreach ($work_types as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $user->last_work_type ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location_id">{{ __('app.timeentries_create_locationid_label') }}</label>
                            <div class="select">
                                <select name="location_id" id="location_id" class="form-control select2">
                                    @foreach ($locations as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $user->last_location ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">{{ __('app.timeentries_create_notes_label') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="{{ __('app.timeentries_create_notes_placeholder') }}">{{ old('notes') }}</textarea>
                            <i class="form-group__bar"></i>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('app.timeentries_create_save_button') }}</button>
                    </form>
                    @else
                    <div class="alert alert-warning text-black" role="alert">
                        <p class="mb-0"><i class="zmdi zmdi-alert-triangle zmdi-hc-fw"></i> {{ __('app.timeentries_create_timerdisabled_alert') }}</p>
                    </div>
                    <form>
                        <div class="form-group">
                            <label for="project_id">{{ __('app.timeentries_create_projectid_label') }}</label>
                            <div class="select">
                                <select name="project_id" id="project_id" class="form-control select2" disabled="disabled">
                                    <option value="">{{ __('app.global_select_prepend') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="work_type_id">{{ __('app.timeentries_create_worktypeid_label') }}</label>
                            <div class="select">
                                <select name="work_type_id" id="work_type_id" class="form-control select2" disabled="disabled">
                                    <option value="">{{ __('app.global_select_prepend') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location_id">{{ __('app.timeentries_create_locationid_label') }}</label>
                            <div class="select">
                                <select name="location_id" id="location_id" class="form-control select2" disabled="disabled">
                                    <option value="">{{ __('app.global_select_prepend') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">{{ __('app.timeentries_create_notes_label') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="{{ __('app.timeentries_create_notes_placeholder') }}" disabled="disabled"></textarea>
                            <i class="form-group__bar"></i>
                        </div>

                        <button type="submit" class="btn btn-primary" disabled="disabled">{{ __('app.timeentries_create_save_button') }}</button>
                    </form>
                    @endif
                </div>
                <div class="tab-pane fade @if($errors->any()) active show @endif" id="time_entry" role="tabpanel">
                    @endcan
                    <form action="{{ route('time-entries.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="is_timer" value="0">

                        <div class="form-group">
                            <label for="project_id">{{ __('app.timeentries_create_projectid_label') }}</label>
                            <div class="select">
                                <select name="project_id" id="project_id" class="form-control select2">
                                    @foreach ($projects as $key => $value)
                                    <option value="{{ $key }}" {{ $key == old('last_project', $user->last_project) ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="work_type_id">{{ __('app.timeentries_create_worktypeid_label') }}</label>
                            <div class="select">
                                <select name="work_type_id" id="work_type_id" class="form-control select2">
                                    @foreach ($work_types as $key => $value)
                                    <option value="{{ $key }}" {{ $key == old('last_work_type', $user->last_work_type) ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location_id">{{ __('app.timeentries_create_locationid_label') }}</label>
                            <div class="select">
                                <select name="location_id" id="location_id" class="form-control select2">
                                    @foreach ($locations as $key => $value)
                                    <option value="{{ $key }}" {{ $key == old('last_location', $user->last_location) ? 'selected="selected"' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="start_time">{{ __('app.timeentries_create_starttime_label') }}</label>
                            <input type="text" name="start_time" id="start_time" value="{{ old('start_time', substr($time, 0, -2) . '00') }}" class="form-control datetime-picker-custom @error('start_time') is-invalid @enderror" placeholder="{{ __('app.global_select_prepend') }}">
                            <i class="form-group__bar"></i>
                            @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="end_time">{{ __('app.timeentries_create_endtime_label') }}</label>
                            <input type="text" name="end_time" id="end_time" value="{{ old('end_time', substr($time, 0, -2) . '00') }}" class="form-control datetime-picker-custom @error('end_time') is-invalid @enderror" placeholder="{{ __('app.global_select_prepend') }}">
                            <i class="form-group__bar"></i>
                            @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">{{ __('app.timeentries_create_notes_label') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="{{ __('app.timeentries_create_notes_placeholder') }}">{{ old('notes') }}</textarea>
                            <i class="form-group__bar"></i>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('app.timeentries_create_save_button') }}</button>
                    </form>
                    @can('start.time_entries')
                </div>
            </div>
        </div>
        @endcan
        @else
        <div class="alert alert-warning text-black" role="alert">
            <p class="mb-0"><i class="zmdi zmdi-alert-triangle zmdi-hc-fw"></i> {{ __('app.timeentries_create_nolocations_alert') }}</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('.datetime-picker-custom').flatpickr({
            altFormat: "d M Y H:i",
            altInput: true,
            dateFormat: "Y-m-d H:i:S",
            enableTime: true,
            nextArrow: '<i class="zmdi zmdi-long-arrow-right zmdi-hc-fw" />',
            prevArrow: '<i class="zmdi zmdi-long-arrow-left zmdi-hc-fw" />',
            time_24hr: true
        });
    });
</script>
@endpush