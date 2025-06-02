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
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            cols="30"
            rows="10"
            placeholder="{{ $placeholder ?? '' }}"
            class="{{ $class ?? null }} form-control{{ $errors->has($name) ? ' border-danger' : null}}"
            autocomplete="off"
        >{{ old($name, $value ?? '') }}</textarea>
        @error($name)
        <span class="form-text text-danger">
                {{ $message }}
            </span>
        @enderror
    </div>

</div>
