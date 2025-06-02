<div class="form-group row">
    @if($label ?? null)
        <label class="col-lg-2 col-form-label text-lg-right" for="{{ $name }}">
            @isset($required)
                <span class="text-danger">*</span>
            @endisset
            {{ $label }}:
        </label>
    @endif
    <div class="col-lg-9">
        <div class="custom-control custom-checkbox mt-2">
            <input type="hidden" name="{{ $name }}" value="0">
            <input type="checkbox" class="custom-control-input" {{ old($name, $value ?? null) == 1 ? "checked" : '' }} name="{{ $name }}" id="{{ $name }}" value="1" >
            <label class="custom-control-label" for="{{ $name }}">{{ __('Kích hoạt') }}</label>
        </div>
        @error($name)
        <span class="form-text text-danger">
            {{ $message }}
        </span>
        @enderror
    </div>
</div>
