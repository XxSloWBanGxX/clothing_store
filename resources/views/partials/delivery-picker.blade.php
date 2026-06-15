@php
    $prefix = $prefix ?? 'delivery';
    $savedAll = $saved['all'] ?? [];
    $savedCarrier = old('delivery_carrier', $saved['carrier'] ?? 'nova_poshta');

    if (old('delivery_carrier')) {
        $savedAll[$savedCarrier] = [
            'city' => old($prefix === 'checkout' ? 'city' : 'delivery_city', ''),
            'city_ref' => old('delivery_city_ref', ''),
            'branch' => old($prefix === 'checkout' ? 'address_line' : 'delivery_branch', ''),
            'branch_ref' => old('delivery_branch_ref', ''),
            'manual' => (bool) old('manual_address', false),
        ];
    }

    $entry = $savedAll[$savedCarrier] ?? [
        'city' => $saved['city'] ?? '',
        'city_ref' => $saved['city_ref'] ?? '',
        'branch' => $saved['branch'] ?? '',
        'branch_ref' => $saved['branch_ref'] ?? '',
        'manual' => $saved['manual'] ?? false,
    ];

    $savedCity = $entry['city'] ?? '';
    $savedCityRef = $entry['city_ref'] ?? '';
    $savedBranch = $entry['branch'] ?? '';
    $savedBranchRef = $entry['branch_ref'] ?? '';
    $manualMode = (bool) ($entry['manual'] ?? false);
@endphp

<div class="delivery-picker" id="{{ $prefix }}Picker"
     data-prefix="{{ $prefix }}"
     data-saved-carrier="{{ $savedCarrier }}"
     data-saved-all='@json($savedAll)'
     data-saved-city="{{ $savedCity }}"
     data-saved-city-ref="{{ $savedCityRef }}"
     data-saved-branch="{{ $savedBranch }}"
     data-saved-branch-ref="{{ $savedBranchRef }}"
     data-manual="{{ $manualMode ? '1' : '0' }}">

    <input type="hidden" name="delivery_carrier" id="{{ $prefix }}_carrier" value="{{ $savedCarrier }}">
    <input type="hidden" name="delivery_city_ref" id="{{ $prefix }}_city_ref" value="{{ $savedCityRef }}">
    <input type="hidden" name="delivery_branch_ref" id="{{ $prefix }}_branch_ref" value="{{ $savedBranchRef }}">
    @if ($prefix === 'checkout')
        <input type="hidden" name="delivery_method" id="{{ $prefix }}_method" value="{{ old('delivery_method', $savedCarrier) }}">
        <input type="hidden" name="city" id="{{ $prefix }}_city_hidden" value="{{ old('city', $savedCity) }}">
        <input type="hidden" name="address_line" id="{{ $prefix }}_address_hidden" value="{{ old('address_line', $savedBranch) }}">
    @else
        <input type="hidden" name="delivery_city" id="{{ $prefix }}_city_hidden" value="{{ $savedCity }}">
        <input type="hidden" name="delivery_branch" id="{{ $prefix }}_branch_hidden" value="{{ $savedBranch }}">
    @endif

    <div class="delivery-carrier-tabs">
        <button type="button" class="delivery-carrier-tab {{ $savedCarrier === 'nova_poshta' ? 'active' : '' }}" data-carrier="nova_poshta">Нова Пошта</button>
        <button type="button" class="delivery-carrier-tab {{ $savedCarrier === 'ukrposhta' ? 'active' : '' }}" data-carrier="ukrposhta">Укрпошта</button>
        <button type="button" class="delivery-carrier-tab {{ $savedCarrier === 'meest' ? 'active' : '' }}" data-carrier="meest">Meest</button>
        <button type="button" class="delivery-carrier-tab {{ $savedCarrier === 'courier' ? 'active' : '' }}" data-carrier="courier">Курʼєр</button>
        <button type="button" class="delivery-carrier-tab {{ $savedCarrier === 'pickup' ? 'active' : '' }}" data-carrier="pickup">Самовивіз</button>
    </div>

    <p class="delivery-api-note" id="{{ $prefix }}_api_note"></p>

    <div class="delivery-auto-block" id="{{ $prefix }}_auto_block">
        <div class="staff-field delivery-field-with-suggest">
            <label for="{{ $prefix }}_city_search">Місто</label>
            <input type="text" id="{{ $prefix }}_city_search" placeholder="Почни вводити назву міста..." autocomplete="off" value="{{ $savedCity }}">
            <div class="delivery-suggest" id="{{ $prefix }}_city_suggest"></div>
        </div>

        <div class="staff-field delivery-field-with-suggest">
            <label for="{{ $prefix }}_point_search">Відділення / поштомат</label>
            <input type="text" id="{{ $prefix }}_point_search" placeholder="Почни вводити номер або адресу..." autocomplete="off" value="{{ $savedBranch }}" {{ $savedCityRef ? '' : 'disabled' }}>
            <div class="delivery-suggest" id="{{ $prefix }}_point_suggest"></div>
        </div>

        <div class="delivery-selected" id="{{ $prefix }}_selected">
            @if ($savedCity && $savedBranch)
                <strong>Обрано:</strong> {{ $savedCity }}, {{ $savedBranch }}
            @endif
        </div>

        <p class="delivery-suggest-hint" id="{{ $prefix }}_point_hint" style="display:none;">Місто обрано. Почни вводити номер або адресу відділення.</p>

        <button type="button" class="delivery-manual-link" id="{{ $prefix }}_manual_link">Не знайшов у списку? Ввести вручну</button>
    </div>

    <div class="delivery-manual-block" id="{{ $prefix }}_manual_block" style="display:none;">
        <input type="hidden" name="manual_address" id="{{ $prefix }}_manual" value="0">
        <div class="staff-field">
            <label for="{{ $prefix }}_manual_city">Місто</label>
            <input type="text" id="{{ $prefix }}_manual_city" value="{{ $savedCity }}">
        </div>
        <div class="staff-field">
            <label for="{{ $prefix }}_manual_address">Адреса / відділення</label>
            <input type="text" id="{{ $prefix }}_manual_address" value="{{ $savedBranch }}">
        </div>
        <button type="button" class="delivery-manual-link" id="{{ $prefix }}_auto_link">← Повернутись до списку відділень</button>
    </div>
</div>
