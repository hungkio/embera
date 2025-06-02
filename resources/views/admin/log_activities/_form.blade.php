<form method="POST" data-block id="log">
    <div class="d-flex align-items-start flex-column flex-md-row">
        <!-- Left content -->
        <div class="w-100 order-2 order-md-1 left-content">
            <div class="row">
                <div class="col-md-12">
                    <x-card>
                        <fieldset>
                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                {{ __('Chi tiết thao tác') }}
                            </legend>
                            <div class="collapse show" id="general">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label text-lg-right" for="body">
                                        {{ __('Tiêu đề') }} :
                                    </label>
                                    <div class="col-lg-8">
                                        <label class="form-control" placeholder="{{ __('Tiêu đề') }}">
                                            {{ $activity->log_name }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label text-lg-right" for="body">
                                        {{ __('Hành động') }} :
                                    </label>
                                    <div class="col-lg-8">
                                        <label class="form-control" placeholder="{{ __('Hành động') }}">
                                            {{ \App\DataTables\LogActivityDataTable::ACTION[$activity->description] }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label text-lg-right" for="body">
                                        {{ __('Tên bảng được thao tác') }} :
                                    </label>
                                    <div class="col-lg-8">
                                        <label class="form-control" placeholder="{{ __('Tên bảng được thao tác') }}">
                                            {{ \App\DataTables\LogActivityDataTable::TABLE[$activity->subject_type] }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label text-lg-right" for="body">
                                        {{ __('ID bản ghi được thao tác') }} :
                                    </label>
                                    <div class="col-lg-8">
                                        <label class="form-control" placeholder="{{ __('ID bản ghi được thao tác') }}">
                                            {{ $activity->subject_id }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label text-lg-right" for="body">
                                        {{ __('Thao tác bởi') }} :
                                    </label>
                                    <div class="col-lg-8">
                                        <label class="form-control" placeholder="{{ __('Thao tác bởi') }}">
                                            {{ $activity->causer->full_name ?? '' }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label text-lg-right" for="body">
                                        {{ __('Dữ liệu khác') }} :
                                    </label>
                                    @if(!$activity->properties->isEmpty())
                                        <div class="col-lg-8">
                                            @foreach($activity->properties as $key => $properties)
                                                @if(is_array($properties))
                                                    @foreach($properties as $sub_key => $property)
                                                        <span class="pl-2">{{ $sub_key . ' : ' . $property }}</span> <br>
                                                    @endforeach
                                                @else
                                                    {{ $key . ' : ' . $properties }} <br>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </fieldset>
                    </x-card>
                    <div class="d-flex justify-content-center align-items-center action-div" id="action-form">
                        <div class="btn-group ml-3">
                            <a class="btn btn-primary btn-block" href="{{ route('admin.log-activities.index') }}">{{ __('Quay lại') }}</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /left content -->
    </div>
</form>

@push('js')
    {!! JsValidator::formRequest('App\Http\Requests\Admin\PageRequest', '#page'); !!}
@endpush()
