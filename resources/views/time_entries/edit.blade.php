@extends('layouts.app')

@section('title', __('app.menu_title_timeentries'))

@section('content')
<header class="content__title">
    <h1>{{ __('app.menu_title_timeentries') }}</h1>
</header>

@if ($time_entry->user->id != Auth::user()->id)
@can('show.time_entries.others')
<div class="alert alert-warning text-black" role="alert">
    <p class="mb-0"><i class="zmdi zmdi-alert-triangle zmdi-hc-fw"></i> {{ __('app.timeentries_edit_warning_itemsboundbyuser') }}</p>
</div>
@endcan
@endif

<div class="card">
    <div class="card-body">
        <h4 class="card-title">{{ __('app.timeentries_edit_card_title') }}</h4>

        @if (count($locations) > 0)
        <form action="{{ route('time-entries.update', $time_entry->id) }}" method="post">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="project_id">{{ __('app.timeentries_edit_projectid_label') }}</label>
                <div class="select">
                    <select name="project_id" id="project_id" class="form-control select2">
                        @foreach ($projects as $key => $value)
                        <option value="{{ $key }}" {{ $key == old('project_id', $time_entry->project_id) ? 'selected="selected"' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="work_type_id">{{ __('app.timeentries_edit_worktypeid_label') }}</label>
                <div class="select">
                    <select name="work_type_id" id="work_type_id" class="form-control select2">
                        @foreach ($work_types as $key => $value)
                        <option value="{{ $key }}" {{ $key == old('work_type_id', $time_entry->work_type_id) ? 'selected="selected"' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="location_id">{{ __('app.timeentries_edit_locationid_label') }}</label>
                <div class="select">
                    <select name="location_id" id="location_id" class="form-control select2">
                        @foreach ($locations as $key => $value)
                        <option value="{{ $key }}" {{ $key == old('location_id', $time_entry->location_id) ? 'selected="selected"' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if (!$time_entry->is_timer)
            <div class="form-group">
                <label for="start_time">{{ __('app.timeentries_edit_starttime_label') }}</label>
                <input type="text" name="start_time" id="start_time" value="{{ old('start_time', $time_entry->start_time) }}" class="form-control datetime-picker-custom @error('start_time') is-invalid @enderror" placeholder="{{ __('app.global_select_prepend') }}">
                <i class="form-group__bar"></i>
                @error('start_time')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="end_time">{{ __('app.timeentries_edit_endtime_label') }}</label>
                <input type="text" name="end_time" id="end_time" value="{{ old('end_time', $time_entry->end_time) }}" class="form-control datetime-picker-custom @error('end_time') is-invalid @enderror" placeholder="{{ __('app.global_select_prepend') }}">
                <i class="form-group__bar"></i>
                @error('end_time')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endif

            <div class="form-group">
                <label for="notes">{{ __('app.timeentries_edit_notes_label') }}</label>
                <textarea name="notes" id="notes" class="form-control" rows="5" placeholder="{{ __('app.timeentries_edit_notes_placeholder') }}">{{ old('notes', $time_entry->notes) }}</textarea>
                <i class="form-group__bar"></i>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('app.timeentries_edit_save_button') }}</button>
        </form>
        @else
        <div class="alert alert-warning text-black" role="alert">
            <p class="mb-0"><i class="zmdi zmdi-alert-triangle zmdi-hc-fw"></i> {{ __('app.timeentries_edit_nolocations_alert') }}</p>
        </div>
        @endif
    </div>
</div>
@endsection

@if (!$time_entry->is_timer)
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('.datetime-picker-custom').flatpickr({
            altFormat: "d M Y H:i",
            altInput: true,
            dateFormat: "Y-m-d H:i:S",
            enableTime: true,
            nextArrow: '<i class="zmdi zmdi-long-arrow-right" />',
            prevArrow: '<i class="zmdi zmdi-long-arrow-left" />',
            time_24hr: true
        });
    });
</script>
@endpush
@endif
