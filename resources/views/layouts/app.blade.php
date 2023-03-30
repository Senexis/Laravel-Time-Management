<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    @include('partials.head')
</head>

<body data-ma-theme="blue">
    <main class="main">
        <div id="sticky-wrapper" class="sticky-wrapper" style="height: 72px;">
            <header class="header">
                @include('partials.header')
            </header>
        </div>

        @if (Auth::user())
        <aside class="sidebar">
            <div class="scrollbar-inner">
                @include('partials.sidebar')
            </div>
        </aside>
        @endif

        <section class="content">
            @include('partials.flash')

            @isset($wide_container)
            @yield('content')
            @else
            <div class="content__inner">
                @yield('content')
            </div>
            @endisset

            <footer class="footer">
                @include('partials.footer')
            </footer>
        </section>

        @if ($send_feedback)
        <div class="modal fade" id="modal-feedback" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title pull-left">{{ __('app.layout_feedback_modal_title') }}</h5>
                    </div>
                    <form action="{{ route('feedback.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="host" value="{{ request()->getHttpHost() }}">
                        <input type="hidden" name="url" value="/{{ count(request()->segments()) > 0 ? implode('/', request()->segments()) : 'home' }}">

                        <div class="modal-body">
                            <p class="mb-4">{{ __('app.layout_feedback_modal_body') }}</p>

                            <div class="form-group">
                                <label for="category">{{ __('app.layout_feedback_modal_category_label') }}</label>
                                <div class="select">
                                    <select name="category" id="category" class="form-control select2">
                                        <option value="bug">{{ __('app.layout_feedback_modal_category_bug_option') }}</option>
                                        <option value="suggestion">{{ __('app.layout_feedback_modal_category_suggestion_option') }}</option>
                                        <option value="feature">{{ __('app.layout_feedback_modal_category_feature_option') }}</option>
                                        <option value="other">{{ __('app.layout_feedback_modal_category_other_option') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="title">{{ __('app.layout_feedback_modal_title_label') }}</label>
                                <input type="text" name="title" id="title" class="form-control title-text-counter" placeholder="{{ __('app.layout_feedback_modal_title_placeholder') }}" autocomplete="off">
                                <i class="form-group__bar"></i>
                            </div>

                            <div class="form-group">
                                <label for="message">{{ __('app.layout_feedback_modal_message_label') }}</label>
                                <textarea name="message" id="message" class="form-control message-text-counter" rows="5" placeholder="{{ __('app.layout_feedback_modal_message_placeholder') }}" autocomplete="off"></textarea>
                                <i class="form-group__bar"></i>
                            </div>

                            <div class="form-group">
                                <label>{{ __('app.layout_feedback_modal_options_label') }}</label>
                                <div class="checkbox">
                                    <input type="checkbox" name="include_host" id="include_host" value="1" checked="checked">
                                    <label class="checkbox__label" for="include_host">{{ __('app.layout_feedback_modal_includehost_label') }}</label>
                                </div>

                                <div class="checkbox">
                                    <input type="checkbox" name="include_url" id="include_url" value="1" checked="checked">
                                    <label class="checkbox__label" for="include_url">{{ __('app.layout_feedback_modal_includeurl_label') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" value="{{ __('app.layout_feedback_modal_send_button') }}" class="btn btn-primary text-uppercase">
                            <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('app.layout_feedback_modal_close_button') }}</button>
                        </div>
                    </form>

                    @push('scripts')
                    <script>
                        $(document).ready(function() {
                            var titleMinValid = false;
                            var titleMaxValid = true;
                            var messageMinValid = false;
                            var messageMaxValid = true;

                            $('.title-text-counter').each(function() {
                                var minLength = 1;
                                var maxLength = 128;

                                updateSubmit();

                                $(this).textcounter({
                                    min: minLength,
                                    max: maxLength,
                                    stopInputAtMaximum: false,
                                    inputErrorClass: 'is-invalid',
                                    counterErrorClass: 'invalid-feedback',
                                    counterText: '',
                                    countExtendedCharacters: true,
                                    minimumErrorText: "{{ __('validation.required', ['attribute' => strtolower(__('app.layout_feedback_modal_title_label'))]) }}",
                                    maximumErrorText: "{{ __('validation.max.string', ['attribute' => strtolower(__('app.layout_feedback_modal_title_label')), 'max' => 128]) }}",
                                    minunder: function(el) {
                                        titleMinValid = false;
                                        updateSubmit();
                                    },
                                    maxunder: function(el) {
                                        titleMaxValid = true;
                                        updateSubmit();
                                    },
                                    mincount: function(el) {
                                        titleMinValid = true;
                                        updateSubmit();
                                    },
                                    maxcount: function(el) {
                                        titleMaxValid = false;
                                        updateSubmit();
                                    },
                                });
                            });

                            $('.message-text-counter').each(function() {
                                var minLength = 1;
                                var maxLength = 1024;

                                updateSubmit();

                                $(this).textcounter({
                                    min: minLength,
                                    max: maxLength,
                                    stopInputAtMaximum: false,
                                    inputErrorClass: 'is-invalid',
                                    counterErrorClass: 'invalid-feedback',
                                    counterText: '',
                                    countExtendedCharacters: true,
                                    minimumErrorText: "{{ __('validation.required', ['attribute' => strtolower(__('app.layout_feedback_modal_message_label'))]) }}",
                                    maximumErrorText: "{{ __('validation.max.string', ['attribute' => strtolower(__('app.layout_feedback_modal_message_label')), 'max' => 1024]) }}",
                                    minunder: function(el) {
                                        messageMinValid = false;
                                        updateSubmit();
                                    },
                                    maxunder: function(el) {
                                        messageMaxValid = true;
                                        updateSubmit();
                                    },
                                    mincount: function(el) {
                                        messageMinValid = true;
                                        updateSubmit();
                                    },
                                    maxcount: function(el) {
                                        messageMaxValid = false;
                                        updateSubmit();
                                    },
                                });
                            });

                            function updateSubmit() {
                                if (titleMinValid && titleMaxValid && messageMinValid && messageMaxValid) {
                                    $('#modal-feedback input[type=submit]').removeAttr('disabled');
                                } else {
                                    $('#modal-feedback input[type=submit]').attr('disabled', 'disabled');
                                }
                            }
                        });
                    </script>
                    @endpush
                </div>
            </div>
        </div>
        @endif
    </main>

    @include('partials.scripts')
</body>

</html>
