@if (session()->has('success'))
    <script>
        toastr.success('{{ session('success') }}', '{{ __('Thành công !') }}');
    </script>
@endif

@if (session()->has('error'))
    <script>
        toastr.error('{{ session('error') }}', '{{ __('Không thành công !') }}');
    </script>
@endif

@if (session()->has('warning'))
    <script>
        toastr.warning('{{ session('warning') }}', '{{ __('Cảnh báo !') }}');
    </script>
@endif
