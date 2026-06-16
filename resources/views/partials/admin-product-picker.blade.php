@props([
    'products' => [],
    'selected' => [],
    'inputName' => 'product_ids[]',
])

@php
    $selected = array_map('intval', (array) $selected);
    $categoryOptions = collect($products)
        ->pluck('category_name', 'category_id')
        ->filter()
        ->unique()
        ->sort();
@endphp

<div class="adm-product-picker-v2" data-product-picker>
    <div class="adm-picker-toolbar">
        <input
            type="search"
            class="adm-picker-search"
            placeholder="Пошук за назвою або категорією…"
            data-picker-search
            autocomplete="off"
        >
        <select class="adm-picker-category" data-picker-category>
            <option value="">Усі категорії</option>
            @foreach ($categoryOptions as $catId => $catName)
                <option value="{{ $catId }}">{{ $catName }}</option>
            @endforeach
        </select>
    </div>

    <div class="adm-picker-actions">
        <span class="adm-picker-count" data-picker-count>Обрано: {{ count($selected) }}</span>
        <div class="adm-picker-action-btns">
            <button type="button" class="btn btn-light btn-sm" data-picker-select-visible>Обрати видимі</button>
            <button type="button" class="btn btn-light btn-sm" data-picker-clear-visible>Зняти видимі</button>
            <button type="button" class="btn btn-light btn-sm" data-picker-clear-all>Зняти всі</button>
        </div>
    </div>

    <div class="adm-picker-list" data-picker-list>
        @forelse ($products as $product)
            @php
                $pid = (int) $product->id;
                $isChecked = in_array($pid, $selected, true);
                $searchText = mb_strtolower(trim(($product->name ?? '') . ' ' . ($product->category_name ?? '')));
            @endphp
            <label
                class="adm-picker-item {{ $isChecked ? 'is-checked' : '' }}"
                data-picker-item
                data-id="{{ $pid }}"
                data-category="{{ (int) ($product->category_id ?? 0) }}"
                data-search="{{ e($searchText) }}"
                data-name="{{ e(mb_strtolower($product->name ?? '')) }}"
            >
                <input
                    type="checkbox"
                    name="{{ $inputName }}"
                    value="{{ $pid }}"
                    {{ $isChecked ? 'checked' : '' }}
                    data-picker-checkbox
                >
                <span class="adm-picker-item-check" aria-hidden="true"></span>
                <span class="adm-picker-item-body">
                    <strong>{{ $product->name }}</strong>
                    <small>
                        {{ $product->category_name ?? 'Без категорії' }}
                        · {{ number_format((float) ($product->price ?? 0), 0, '.', ' ') }} грн
                    </small>
                </span>
            </label>
        @empty
            <p class="adm-picker-empty">Товарів у каталозі ще немає.</p>
        @endforelse
    </div>

    <p class="adm-picker-footnote" data-picker-visible-count></p>
</div>
