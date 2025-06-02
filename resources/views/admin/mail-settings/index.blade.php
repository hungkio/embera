@extends('admin.layouts.master')

@section('title', 'Chiến dịch gửi mail')

@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@push('css')
    <style>
        .mail-setting {
            background: #ffffff;
            box-shadow: 0 1px 4px 0 rgba(0, 0, 0, .1);
            border-radius: 4px;
            overflow: hidden;
        }

        .email_body img {
            max-width: 100%;
        }

        .email_body {
            text-align: left;
        }

        label.error {
            color: red !important;
        }

        input.error:not(label) {
            border: 1px solid red;
        }

        .panel.panel-default {
            border-color: #cccccc;
            margin-bottom: 10px;
        }

        .panel-heading {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .125);
        }

        .panel-heading a:not(.collapsed) + .header-elements .list-icons a:after {
            -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
        }

        textarea.form-control {
            max-width: 100%;
        }

        .panel-heading + .panel-body {
            padding-top: 15px;
        }

        .panel-heading a {
            padding: .9375rem 1.25rem;
            display: block;
            color: #333;
        }

        .panel-collapse {
            padding: 15px;
            background: #fff;
        }

        .panel-body label {
            font-size: 14px;
            color: #333;
        }

        .form-control {
            background: #fff;
        }

        body {
            color: rgba(0, 0, 0, 1) !important;
        }

        .uniform-checker {
            display: inline-block;
        }

        .sidebar-component {
            display: block;
        }
    </style>
@endpush

@push('js')
    <script src="{{ asset('backend/js/editor-admin.js') }}"></script>
    <script>
        $('.form-control-uniform').uniform();

        $('.select2').select2({
            placeholder: "{{ __('-- Chọn email --') }}",
        });

        function disableSelect(val) {
            if (val && (val == 1 || val == 2)) {
                if (val == 2) {
                    $('select[name="default_group"]').prop('disabled', false)
                } else {
                    $('select[name="default_group"]').prop('disabled', true)
                }
                $('select.select2').prop('disabled', true)
            } else {
                $('select.select2').prop('disabled', false)
                $('select[name="default_group"]').prop('disabled', true)
            }
        }

        function convertToSlug(str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            var from = "àăáäâèéëêìíïîòóöôùúüûñç·/_,:;";
            var to = "aăaaaeeeeiiiioooouuuunc------";
            for (var i = 0, l = from.length; i < l; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            return str;
        }

        $(function () {
            let form_id = '#form-mail-settings';
            $(form_id).on('submit', function () {
                tinyMCE.triggerSave();
                let form = $(form_id)
                    , formdata = form.serialize();
                if ($(form_id).valid()) {
                    $.post('{{route('admin.mail-settings.save')}}', formdata, function (resp) {
                        if ('error' in resp) {
                            showMessage('error', resp.message);
                        } else {
                            showMessage('success', resp.message);
                            setTimeout(function () {
                                location.reload();
                            }, 1e3);
                        }
                    })
                        .fail(function () {
                            showMessage('error', '{{ __('Có lỗi xảy ra, vui lòng thử lại !') }}');
                            location.reload();
                        });
                }
                return false;
            });

            //
            $('.js-send-mail-now').on('click', function () {
                if ($('#form-mail-settings').valid()) {
                    let mail_key = $(this).data('mail-key');
                    let slug = $(this).data('slug');

                    $('<input>').attr({
                        type: 'hidden',
                        id: 'mail_key',
                        name: 'mail_key',
                        value: mail_key
                    }).appendTo('#form-mail-settings');
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'slug',
                        name: 'slug',
                        value: slug
                    }).appendTo('#form-mail-settings');
                }
                $('#form-mail-settings').submit()
                return false;
            });
            $('#name_campaign').change(function () {
                let slug = convertToSlug($(this).val())
                $('#tab-slug').val(slug)
            })

            $('#create-campaign').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 255
                    },
                },
                messages: {
                    name: {
                        required: "{{__('Vui lòng nhập tên chiến dịch!')}}",
                        maxlength: "{{__('Tên chiến dịch không được dài quá 255 ký tự!')}}"
                    },
                },
            });

            $('#form-mail-settings').validate({
                rules: {
                    default_subject: {
                        required: true,
                        maxlength: 255
                    },
                    "default_user[]": {
                        required: function (el) {
                            return $(el).val() == '' && !$("input[name='default_all[]']").is(':checked')
                        }
                    }
                },
                messages: {
                    default_subject: {
                        required: "{{__('Vui lòng nhập tiêu đề!')}}",
                        maxlength: "{{__('Tiêu đề quá dài !')}}"
                    },
                    "default_user[]": {
                        required: "{{__('Vui lòng chọn người nhận!')}}",
                    }
                },
            });

            $("select[name='default_user[]']").change(function () {
                if ($(this).val() || $("input[name='default_all[]']").is(':checked')) {
                    $(this).closest('div').find('label').text('')
                }
            })

            $('input[name="default_all[]"').change(function () {
                if ($("select[name='default_user[]']").val() || $(this).is(':checked')) {
                    $('label[for="default_user[]"]').text('')
                }
            })

            $('#create-campaign').submit(function (e) {
                // Stop the browser from submitting the form.
                e.preventDefault();

                // Serialize the form data.
                var formData = $(this).serialize();
                // Submit the form using AJAX.
                if ($('#create-campaign').valid()) {
                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: formData
                    }).done(function (response) {
                        showMessage('success', response.message);
                        setTimeout(function () {
                            location.reload();
                        }, 1e3);
                    }).fail(function (data) {
                        let msg = ''
                        if (data.message) {
                            msg = data.message
                        } else {
                            msg = data.responseJSON.errors.tab[0]
                        }
                        showMessage('error', msg);
                    });
                }
            });
            $('.preview_email').click(function () {
                $('.email_body').html($('textarea.wysiwyg').val())
            })
            if ($('input[name="default_all[]"]').is(":checked")) {
                $('select.select2').prop('disabled', true)
            }
            $('input[name="default_all[]"]').change(function () {
                if ($(this).is(":checked")) {
                    $('select.select2').prop('disabled', true)
                } else {
                    $('select.select2').prop('disabled', false)
                }
            })

            $("select[name='default_all']").change(function () {

                disableSelect($(this).val())
            })

            disableSelect($("select[name='default_all']").val())

            $(document).on('click', '.js-delete-mail', function () {
                var deleteUrl = $(this).data('url');

                confirmAction(Lang.confirm_delete, function (result) {
                    if (result) {
                        $.ajax({
                            type: 'POST',
                            url: deleteUrl,
                            data: {
                                _method: "DELETE"
                            },
                            success: function (res) {
                                if (res.status == 'error') {
                                    showMessage('error', res.message);
                                } else {
                                    showMessage('success', res.message);
                                }
                                location.reload();
                            }
                        })
                    }
                })
            })

            $('#exampleModal').on('hidden.bs.modal', function () {
                // do something…
                $('#name_campaign').val('')
                $('#name_campaign').removeClass('error')
                $('#name_campaign-error').text('')
            })
        });
    </script>
@endpush

@section('page-content')
    <div class="d-md-flex align-items-md-start">
        <div class="sidebar sidebar-light sidebar-component sidebar-component-left sidebar-expand-md">
            <div class="sidebar-content">
                <div class="card mb-2">
                    <div class="card-header bg-transparent header-elements-inline">
                        <span class="text-uppercase font-size-sm font-weight-semibold">{{ __('Danh sách') }}</span>
                        @can('mail-settings.create')
                            <span>
                            <button class="btn btn-primary legitRipple btn-save" data-toggle="modal"
                                    data-target="#exampleModal"><i class="fal fa-plus-circle"></i> {{ __('Tạo thêm') }}
                            </button>
                        </span>
                        @endcan
                    </div>

                    <div class="card-body p-0">
                        <ul class="nav nav-sidebar" data-nav-type="accordion">
                            @foreach($tabs as $key => $text)
                                <li class="nav-item">
                                    <a href="{{ route('admin.mail-settings.index', ['tab' => $key]) }}"
                                       class="list-group-item {{ $default_tab == $key ? 'active' : 'text-primary' }}">
                                        {{ $text }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-100 mail-setting">
            <form method="post" id="form-mail-settings">
                <input type="hidden" name="tab" value="{{ $default_tab }}">

                @foreach($groups as $key => $fields)
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="heading{{ $key }}">
                            <div class="d-flex align-items-center">
                                <a class="flex-1 collapsed" role="button" data-toggle="collapse"
                                   href="#collapse{{ $key }}"
                                   aria-expanded="false" aria-controls="collapse{{ $key }}">
                                    {{--                                    {{ \ucwords( \str_replace(['-', '_'], ' ', $key) ) }}--}}
                                    {{ ucwords( __('Thông tin chi tiết') ) }}
                                </a>
                                <div class="header-elements">
                                    <div class="list-icons">
                                        <a class="list-icons-item" data-action="collapse"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="collapse{{ $key }}" class="panel-collapse collapse show" role="tabpanel"
                             aria-labelledby="heading{{ $key }}">
                            <div class="panel-body">
                                @foreach($fields as $field_id => $field_options)
                                    <?php
                                    $field_label = \ucwords(\str_replace(['-', '_'], ' ', $field_id));
                                    $field_name = "{$key}_{$field_id}";
                                    $field_type = is_string($field_options) ? $field_options : $field_options['type'];
                                    $field_value = isset($default_values[$field_name]) ? $default_values[$field_name] : '';

                                    $view_args = [
                                        'name' => $field_name,
                                        'type' => $field_type,
                                        'value' => $field_value,
                                    ];

                                    if (\in_array($field_type, ['checkbox', 'checkboxes', 'radio', 'select2', 'select'])) {
                                        $view_args['options'] = $field_options['options'];
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label
                                            class="control-label">{{ __('validation.labels.mail_' . $field_id) }}</label>
                                        <div>
                                            @include('admin.mail-settings.custom-html-fields',$view_args)
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="panel-footer">
                                @if($groups)
                                    @can('mail-settings.update')
                                        <button type="submit"
                                                class="btn btn-primary legitRipple btn-save ml-2 btn-custom">
                                            <i class="fal fa-pencil"></i>
                                            {{ __('Lưu') }}
                                        </button>
                                    @endcan
                                    <button type="button"
                                            class="btn btn-primary legitRipple ml-2 preview_email btn-custom"
                                            data-toggle="modal"
                                            data-target="#preview_email_modal"
                                    >
                                        <i class="fal fa-search"></i>
                                        {{ __('Xem trước') }}
                                    </button>
                                @endif
                                @can('mail-settings.send')
                                    <button type="button"
                                            class="btn btn-primary legitRipple js-send-mail-now ml-2 btn-custom"
                                            data-slug="{{ request('tab') }}" data-mail-key="{{ $key }}"
                                    >
                                        <i class="fal fa-paper-plane"></i>
                                        {{ __('Gửi') }}
                                    </button>
                                @endcan
                                @can('mail-settings.delete')
                                    @if(request('tab') && (('mail-template' == request('tab') && @$default_values['default_subject']) || 'mail-template' != request('tab')))
                                        <button type="button"
                                                class="btn btn-danger float-right js-delete-mail btn-custom"
                                                data-url="{{ route('admin.mail-settings.delete', request('tab', '')) }}"
                                        >
                                            <i class="far fa-trash-alt"></i>
                                            {{ __('Xoá') }}
                                        </button>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </form>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content" action="{{ route('admin.mail-settings.save') }}" id="create-campaign"
                  method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Tạo chiến dịch gửi mail mới') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">{{ __('Tên chiến dịch') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div>
                            <input type="text" name="name" value="" id="name_campaign" placeholder="Tên chiến dịch"
                                   class="form-control ">
                            <input type="hidden" name="tab" value="" id="tab-slug" class="form-control ">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Đóng') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Lưu') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="preview_email_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Xem trước mẫu email') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Đóng') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        @include('shop.mail.mail-common')
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Đóng') }}</button>
                </div>
            </div>
        </div>
    </div>
@stop
