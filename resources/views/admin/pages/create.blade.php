@extends('admin.layouts.master')

@section('title', __('Tạo'))
@section('page-header')
    <x-page-header>
        {{ Breadcrumbs::render() }}
    </x-page-header>
@stop

@section('page-content')
    @include('admin.pages._form', [
        'url' =>  route('admin.pages.store'),
        'page' => new \App\Domain\Page\Models\Page,
    ])
@stop

@push('js')
    <script src="{{ asset('backend/js/editor-admin.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#title').focus();
        });
        $('.form-check-input-styled').uniform();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.js" integrity="sha512-9WciDs0XP20sojTJ9E7mChDXy6pcO0qHpwbEJID1YVavz2H6QBz5eLoDD8lseZOb2yGT8xDNIV7HIe1ZbuiDWg==" crossorigin="anonymous"></script>
    <script>
        let maxFileUpload = 9;
        Dropzone.autoDiscover = true;
        Dropzone.options.postImages = {
            url: '{{ route('admin.pages.upload.image') }}',
            maxFilesize: 2,
            maxFiles: maxFileUpload,
            addRemoveLinks: true,
            acceptedFiles: "image/*",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (file, response) {
                $('#post-form').append('<input type="hidden" name="new_images[]" value="' + response.file + '">')
                file.previewElement.classList.add("dz-success")
            },
            removedfile: function (file) {
                file.previewElement.remove()
                if (file.hasOwnProperty('id')) {
                    $('#post-form').find('input[name="images[]"][value="' + file.id + '"]').remove()
                } else {
                    $('#post-form').find('input[name="new_images[]"][value="' + file.name + '"]').remove()
                }
            },
            init: function () {
                var myDropzone = this;
                this.on("addedfile", function(file) {
                    file.previewElement.addEventListener("click", function() {
                        myDropzone.removeFile(file);
                    });
                });
                myDropzone.on("maxfilesexceeded", function(file) {
                    this.removeFile(file);
                    console.log('{{ __("Đã đạt đến tệp tối đa !") }}');
                });
            }
        }
    </script>
@endpush
