@props([
    'tabs' => [],
    'activeTab' => 'create',
    'baseUrl' => '',
    'previewUrl' => null,
    'previewLabel' => 'Переглянути на сайті',
])

<aside class="adm-marketing-nav">
    <div class="adm-marketing-nav-head">
        <h2>Розділ</h2>
        <p>Обери дію</p>
    </div>
    <nav class="adm-marketing-nav-list">
        @foreach ($tabs as $key => $tab)
            <a
                href="{{ $baseUrl }}?tab={{ $key }}{{ ! empty($tab['query']) ? '&' . $tab['query'] : '' }}"
                class="adm-marketing-nav-item {{ $activeTab === $key ? 'is-active' : '' }}"
            >
                <span class="adm-marketing-nav-label">{{ $tab['label'] }}</span>
                @if (! empty($tab['desc']))
                    <span class="adm-marketing-nav-desc">{{ $tab['desc'] }}</span>
                @endif
            </a>
        @endforeach
    </nav>
    @if ($previewUrl)
        <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="adm-marketing-preview-link">↗ {{ $previewLabel }}</a>
    @endif
</aside>
