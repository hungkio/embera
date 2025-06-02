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
        <select name="{{ $name }}" id="{{ $name }}" class="form-control form-control-select2 {{ $class ?? null }}" data-placeholder="{{ $placeholder ?? null }}">
            <option></option>
            @foreach($options as $optionValue => $text)
                <option value="{{ $optionValue }}" {{ ( old($name, $value ?? null) == $optionValue) ? 'selected' : null }}>{{ $text }}</option>
            @endforeach
        </select>
        @isset($icon)
            <div class="form-control-feedback">
                <i class="{{ $icon }} text-muted"></i>
            </div>
        @endisset
        @error($name)
            <span class="form-text text-danger">
                {{ $message }}
            </span>
        @enderror
    </div>

</div>
