@if (flash()->message)
    @push('js')
        <script>
            $(function () {
                showMessage('{{ flash()->class }}', '{{ flash()->message }}')
            })
        </script>
    @endpush
@endif


@if($errors->any())
    @push('js')
        <script>
            showMessage('error', '{{ __('Vui lòng kiểm tra thông tin đã nhập !') }}')
        </script>
    @endpush
@endif
