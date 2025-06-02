<form method="POST" action="{{ $action }}">
    @csrf
    @method($method ?? 'POST')
    <button
        type="submit"
        class="{{ $class ?? '' }}"
    >
        {{ $slot }}
    </button>
</form>
