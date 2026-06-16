@props([
    'name' => 'is_active',
    'label',
    'hint' => null,
    'checked' => false,
    'value' => '1',
])

<div class="adm-switch-wrap adm-grid-full">
    <div class="adm-switch-row">
        <div class="adm-switch-copy">
            <strong>{{ $label }}</strong>
            @if ($hint)
                <span>{{ $hint }}</span>
            @endif
        </div>
        <label class="adm-switch" aria-label="{{ $label }}">
            <input type="checkbox" name="{{ $name }}" value="{{ $value }}" {{ $checked ? 'checked' : '' }}>
            <span class="adm-switch-slider" aria-hidden="true"></span>
        </label>
    </div>
</div>
