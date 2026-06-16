@props([
    'name',
    'label',
    'type' => 'text',
    'hint' => null,
    'placeholder' => null,
    'value' => '',
    'rows' => 3,
    'wide' => false,
    'min' => null,
    'max' => null,
    'step' => null,
    'paths' => [],
    'inputId' => null,
])

@php
    $inputId = $inputId ?? ('field_' . $name);
@endphp

<div class="form-group {{ $wide ? 'adm-grid-full' : '' }}">
    <div class="adm-field-head">
        <label for="{{ $inputId }}">{{ $label }}</label>
        @if ($hint)
            <span class="adm-field-hint">{{ $hint }}</span>
        @endif
    </div>

    @if ($type === 'textarea')
        <textarea
            id="{{ $inputId }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            class="adm-field-input"
        >{{ old($name, $value) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($min !== null) min="{{ $min }}" @endif
            @if ($max !== null) max="{{ $max }}" @endif
            @if ($step !== null) step="{{ $step }}" @endif
            class="adm-field-input{{ ! empty($paths) ? ' adm-field-input--path' : '' }}"
            @if (! empty($paths)) data-path-field @endif
        >
    @endif

    @if (! empty($paths))
        <div class="adm-path-chips">
            @foreach ($paths as $pathLabel => $pathUrl)
                <button type="button" class="adm-path-chip" data-path-target="{{ $inputId }}" data-path-value="{{ $pathUrl }}">{{ $pathLabel }}</button>
            @endforeach
        </div>
    @endif

    @error($name)
        <small class="form-error">{{ $message }}</small>
    @enderror
</div>
