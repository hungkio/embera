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
        <input
            autocomplete="new-password"
            type="{{ $type ?? 'text' }}"
            name="{{ $name }}"
            id="{{ $name }}"
            class="form-control{{ $errors->has($name) ? ' border-danger' : null}}"
            placeholder="{{ $placeholder ?? '' }}"
            value="{{ old($name, $value ?? '') }}"
            {{isset($readonly) && $readonly ? 'readonly' : null}}

        >
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
