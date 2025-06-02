@extends('admin.layouts.base')
@push('vendor-js')
    <script src="{{ asset('/backend/global_assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('/backend/global_assets/js/plugins/tables/datatables/extensions/buttons.min.js') }}"></script>
    <script src="{{ asset('/backend/global_assets/js/plugins/tables/datatables/extensions/select.min.js') }}"></script>
    <script src="{{ asset('/backend/global_assets/js/plugins/tables/datatables/extensions/fixed_header.min.js') }}"></script>
    <script src="{{ asset('/backend/global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script src="{{ asset('/backend/global_assets/js/plugins/notifications/bootbox.min.js') }}"></script>
    <script src="{{ asset('/backend/global_assets/js/plugins/notifications/pnotify.min.js') }}"></script>
    <script src="{{ asset('/backend/global_assets/js/plugins/ui/sticky.min.js') }}"></script>
    <script src="{{ asset('/backend/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@section('navbar')
    <!-- Main navbar -->
    @include('admin.layouts._mainNav')
    <!-- /main navbar -->
@stop

@section('content')

    <!-- Page content -->
    <div class="page-content">

        <!-- Main sidebar -->
        @include('admin.layouts._menu')
        <!-- /main sidebar -->


        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Page header -->
            @yield('page-header')
            <!-- /page header -->


            <!-- Content area -->
            <div class="content content-boxed pt-0">

                @include('admin.layouts._alert')

                @yield('page-content')

            </div>
            <!-- /content area -->


            <!-- Footer -->
            @include('admin.layouts._footer')
            <!-- /footer -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

@stop
@push('js')
<script type="text/javascript" src="{{ asset('backend/vendor/jsvalidation/js/jsvalidation.js')}}"></script>
@endpush
