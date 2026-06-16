@extends('admin.layout')

@section('title', 'Налаштування сайту')

@section('admin_content')
@include('partials.admin-flash')

@php
    $s = $settings;
    $pathOptions = [
        'Каталог' => '/catalog',
        'Новинки' => '/new',
        'Знижки' => '/sale',
        'Про нас' => '/about',
        'Головна' => '/',
    ];
    $tabs = [
        'brand' => ['label' => 'Бренд', 'desc' => 'Назва та логотип у шапці', 'preview' => url('/')],
        'contacts' => ['label' => 'Контакти', 'desc' => 'Email, телефон, соцмережі', 'preview' => url('/') . '#contacts'],
        'homepage' => ['label' => 'Головна', 'desc' => 'Hero, переваги, банер', 'preview' => url('/')],
        'footer' => ['label' => 'Футер', 'desc' => 'Низ сайту та смуга', 'preview' => url('/') . '#footer'],
        'shop' => ['label' => 'Магазин', 'desc' => 'Доставка, новинки, відгуки', 'preview' => url('/catalog')],
    ];
    $activeTab = $activeTab ?? 'brand';
@endphp

<form action="{{ url('/admin/settings') }}" method="POST" class="adm-settings-shell" id="admSettingsForm">
    @csrf
    <input type="hidden" name="_active_tab" id="admSettingsActiveTab" value="{{ $activeTab }}">

    <aside class="adm-settings-nav">
        <div class="adm-settings-nav-head">
            <h2>Розділи</h2>
            <p>Обери блок, який хочеш змінити</p>
        </div>
        <nav class="adm-settings-nav-list" role="tablist">
            @foreach ($tabs as $key => $tab)
                <button
                    type="button"
                    class="adm-settings-nav-item {{ $activeTab === $key ? 'is-active' : '' }}"
                    data-tab="{{ $key }}"
                    role="tab"
                    aria-selected="{{ $activeTab === $key ? 'true' : 'false' }}"
                >
                    <span class="adm-settings-nav-label">{{ $tab['label'] }}</span>
                    <span class="adm-settings-nav-desc">{{ $tab['desc'] }}</span>
                </button>
            @endforeach
        </nav>
        <a href="{{ url('/') }}" target="_blank" rel="noopener" class="adm-settings-preview-link">↗ Відкрити сайт</a>
    </aside>

    <div class="adm-settings-main">
        {{-- Бренд --}}
        <section class="adm-settings-panel {{ $activeTab === 'brand' ? 'is-active' : '' }}" data-panel="brand">
            <header class="adm-settings-panel-head">
                <div>
                    <h2>Бренд і логотип</h2>
                    <p>Те, що відвідувач бачить у шапці та футері</p>
                </div>
                <a href="{{ $tabs['brand']['preview'] }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">Переглянути</a>
            </header>

            <div class="adm-settings-card adm-settings-card--preview">
                <span class="adm-settings-card-label">Попередній перегляд логотипу</span>
                <div class="adm-logo-preview" id="admLogoPreview">
                    <span class="adm-logo-preview-lead" id="previewBrandLead">{{ old('brand_lead', $s['brand_lead'] ?? 'CLOTH') }}</span><span class="adm-logo-preview-accent" id="previewBrandAccent">{{ old('brand_accent', $s['brand_accent'] ?? 'STORE') }}</span>
                </div>
            </div>

            <div class="adm-settings-card">
                <h3 class="adm-settings-card-title">Логотип у шапці</h3>
                <div class="admin-form-grid">
                    @include('partials.admin-settings-field', [
                        'name' => 'brand_lead',
                        'label' => 'Перша частина',
                        'hint' => 'Основний колір тексту логотипу',
                        'placeholder' => 'CLOTH',
                        'value' => $s['brand_lead'] ?? '',
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'brand_accent',
                        'label' => 'Акцентна частина',
                        'hint' => 'Виділяється іншим кольором',
                        'placeholder' => 'STORE',
                        'value' => $s['brand_accent'] ?? '',
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'brand_name',
                        'label' => 'Повна назва бренду',
                        'hint' => 'Використовується у футері та службових місцях',
                        'placeholder' => 'CLOTHSTORE',
                        'value' => $s['brand_name'] ?? '',
                        'wide' => true,
                    ])
                </div>
            </div>
        </section>

        {{-- Контакти --}}
        <section class="adm-settings-panel {{ $activeTab === 'contacts' ? 'is-active' : '' }}" data-panel="contacts">
            <header class="adm-settings-panel-head">
                <div>
                    <h2>Контакти та соцмережі</h2>
                    <p>Дані для футера, сторінок і форм підтримки</p>
                </div>
                <a href="{{ $tabs['contacts']['preview'] }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">Переглянути</a>
            </header>

            <div class="adm-settings-card">
                <h3 class="adm-settings-card-title">Звʼязок з клієнтами</h3>
                <div class="admin-form-grid">
                    @include('partials.admin-settings-field', [
                        'name' => 'contact_email',
                        'label' => 'Email магазину',
                        'type' => 'email',
                        'hint' => 'На цю адресу надходитимуть звернення з форм',
                        'placeholder' => 'info@example.com',
                        'value' => $s['contact_email'] ?? '',
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'contact_phone',
                        'label' => 'Телефон',
                        'hint' => 'Формат для відображення на сайті',
                        'placeholder' => '+380 99 000 00 00',
                        'value' => $s['contact_phone'] ?? '',
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'contact_location',
                        'label' => 'Місто / країна',
                        'hint' => 'Короткий рядок під контактами',
                        'placeholder' => 'Україна',
                        'value' => $s['contact_location'] ?? '',
                        'wide' => true,
                    ])
                </div>
            </div>

            <div class="adm-settings-card">
                <h3 class="adm-settings-card-title">Instagram</h3>
                <div class="admin-form-grid">
                    @include('partials.admin-settings-field', [
                        'name' => 'instagram_url',
                        'label' => 'Посилання на профіль',
                        'type' => 'url',
                        'hint' => 'Повне посилання з https://',
                        'placeholder' => 'https://www.instagram.com/...',
                        'value' => $s['instagram_url'] ?? '',
                        'wide' => true,
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'instagram_handle',
                        'label' => 'Нікнейм для відображення',
                        'hint' => 'Наприклад @tori_cloth.store',
                        'placeholder' => '@brand',
                        'value' => $s['instagram_handle'] ?? '',
                    ])
                </div>
            </div>
        </section>

        {{-- Головна --}}
        <section class="adm-settings-panel {{ $activeTab === 'homepage' ? 'is-active' : '' }}" data-panel="homepage">
            <header class="adm-settings-panel-head">
                <div>
                    <h2>Головна сторінка</h2>
                    <p>Перший екран, переваги магазину та промо-банер</p>
                </div>
                <a href="{{ $tabs['homepage']['preview'] }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">Переглянути</a>
            </header>

            <div class="adm-settings-card">
                <h3 class="adm-settings-card-title">Hero — перший екран</h3>
                <p class="adm-settings-card-lead">Великий блок зверху головної: заголовок, текст і дві кнопки</p>
                <div class="admin-form-grid">
                    @include('partials.admin-settings-field', [
                        'name' => 'hero_badge',
                        'label' => 'Мітка зверху',
                        'hint' => 'Невеликий бейдж над заголовком',
                        'placeholder' => 'NEW COLLECTION',
                        'value' => $s['hero_badge'] ?? '',
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'hero_title',
                        'label' => 'Заголовок',
                        'type' => 'textarea',
                        'rows' => 2,
                        'hint' => 'Можна переносити рядок — Enter у полі',
                        'placeholder' => "Стиль, який\nговорить за тебе",
                        'value' => $s['hero_title'] ?? '',
                        'wide' => true,
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'hero_text',
                        'label' => 'Опис під заголовком',
                        'type' => 'textarea',
                        'rows' => 3,
                        'value' => $s['hero_text'] ?? '',
                        'wide' => true,
                    ])
                </div>

                <div class="adm-settings-subgrid">
                    <div class="adm-settings-subblock">
                        <h4>Кнопка «Основна»</h4>
                        <div class="admin-form-grid">
                            @include('partials.admin-settings-field', [
                                'name' => 'hero_btn1_text',
                                'label' => 'Текст',
                                'placeholder' => 'Перейти в каталог',
                                'value' => $s['hero_btn1_text'] ?? '',
                            ])
                            @include('partials.admin-settings-field', [
                                'name' => 'hero_btn1_url',
                                'label' => 'Куди веде',
                                'hint' => 'Шлях на сайті або повне посилання',
                                'placeholder' => '/catalog',
                                'value' => $s['hero_btn1_url'] ?? '',
                                'paths' => $pathOptions,
                            ])
                        </div>
                    </div>
                    <div class="adm-settings-subblock">
                        <h4>Кнопка «Додаткова»</h4>
                        <div class="admin-form-grid">
                            @include('partials.admin-settings-field', [
                                'name' => 'hero_btn2_text',
                                'label' => 'Текст',
                                'placeholder' => 'Дивитися новинки',
                                'value' => $s['hero_btn2_text'] ?? '',
                            ])
                            @include('partials.admin-settings-field', [
                                'name' => 'hero_btn2_url',
                                'label' => 'Куди веде',
                                'placeholder' => '/new',
                                'value' => $s['hero_btn2_url'] ?? '',
                                'paths' => $pathOptions,
                            ])
                        </div>
                    </div>
                </div>

                <h4 class="adm-settings-mini-title">Цифри під hero (3 блоки)</h4>
                <div class="adm-stat-cards">
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="adm-stat-card">
                            <span class="adm-stat-card-num">#{{ $i }}</span>
                            @include('partials.admin-settings-field', [
                                'name' => "hero_stat{$i}_value",
                                'label' => 'Число',
                                'placeholder' => $i === 1 ? '500+' : ($i === 2 ? '24/7' : '100%'),
                                'value' => $s["hero_stat{$i}_value"] ?? '',
                            ])
                            @include('partials.admin-settings-field', [
                                'name' => "hero_stat{$i}_label",
                                'label' => 'Підпис',
                                'placeholder' => 'Товарів',
                                'value' => $s["hero_stat{$i}_label"] ?? '',
                            ])
                        </div>
                    @endfor
                </div>
            </div>

            <div class="adm-settings-card">
                <h3 class="adm-settings-card-title">Три переваги магазину</h3>
                <p class="adm-settings-card-lead">Картки під hero-блоком — коротко, чому варто купувати тут</p>
                <div class="adm-feature-cards">
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="adm-feature-card">
                            <span class="adm-feature-card-badge">Блок {{ $i }}</span>
                            @include('partials.admin-settings-field', [
                                'name' => "feature{$i}_title",
                                'label' => 'Заголовок',
                                'placeholder' => 'Швидке оформлення',
                                'value' => $s["feature{$i}_title"] ?? '',
                                'wide' => true,
                            ])
                            @include('partials.admin-settings-field', [
                                'name' => "feature{$i}_text",
                                'label' => 'Текст',
                                'type' => 'textarea',
                                'rows' => 2,
                                'value' => $s["feature{$i}_text"] ?? '',
                                'wide' => true,
                            ])
                        </div>
                    @endfor
                </div>
            </div>

            <div class="adm-settings-card">
                <h3 class="adm-settings-card-title">Промо-банер внизу головної</h3>
                <p class="adm-settings-card-lead">Заклик до дії перед футером</p>
                <div class="admin-form-grid">
                    @include('partials.admin-settings-field', [
                        'name' => 'banner_label',
                        'label' => 'Мітка',
                        'placeholder' => 'COLLECTION',
                        'value' => $s['banner_label'] ?? '',
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'banner_title',
                        'label' => 'Заголовок',
                        'placeholder' => 'Онови свій гардероб вже сьогодні',
                        'value' => $s['banner_title'] ?? '',
                        'wide' => true,
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'banner_text',
                        'label' => 'Текст',
                        'type' => 'textarea',
                        'rows' => 2,
                        'value' => $s['banner_text'] ?? '',
                        'wide' => true,
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'banner_btn_text',
                        'label' => 'Текст кнопки',
                        'placeholder' => 'До покупок',
                        'value' => $s['banner_btn_text'] ?? '',
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'banner_btn_url',
                        'label' => 'Посилання кнопки',
                        'placeholder' => '/catalog',
                        'value' => $s['banner_btn_url'] ?? '',
                        'paths' => $pathOptions,
                    ])
                </div>
            </div>
        </section>

        {{-- Футер --}}
        <section class="adm-settings-panel {{ $activeTab === 'footer' ? 'is-active' : '' }}" data-panel="footer">
            <header class="adm-settings-panel-head">
                <div>
                    <h2>Футер і верхня смуга</h2>
                    <p>Низ кожної сторінки та тонка смуга над футером</p>
                </div>
                <a href="{{ $tabs['footer']['preview'] }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">Переглянути</a>
            </header>

            <div class="adm-settings-card">
                <h3 class="adm-settings-card-title">Опис бренду у футері</h3>
                <div class="admin-form-grid">
                    @include('partials.admin-settings-field', [
                        'name' => 'footer_description',
                        'label' => 'Короткий текст про магазин',
                        'type' => 'textarea',
                        'rows' => 3,
                        'hint' => '1–2 речення під логотипом у футері',
                        'value' => $s['footer_description'] ?? '',
                        'wide' => true,
                    ])
                </div>
            </div>

            <div class="adm-settings-card">
                <h3 class="adm-settings-card-title">Смуга над футером</h3>
                <p class="adm-settings-card-lead">Тонка смуга з закликом — наприклад, «Нова колекція вже в каталозі»</p>
                <div class="admin-form-grid">
                    @include('partials.admin-settings-field', [
                        'name' => 'footer_strip_text',
                        'label' => 'Текст смуги',
                        'placeholder' => 'Нова колекція вже в каталозі',
                        'value' => $s['footer_strip_text'] ?? '',
                        'wide' => true,
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'footer_strip_link_text',
                        'label' => 'Текст посилання',
                        'placeholder' => 'Дивитись новинки →',
                        'value' => $s['footer_strip_link_text'] ?? '',
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'footer_strip_link_url',
                        'label' => 'Куди веде посилання',
                        'placeholder' => '/new',
                        'value' => $s['footer_strip_link_url'] ?? '',
                        'paths' => $pathOptions,
                    ])
                </div>
            </div>
        </section>

        {{-- Магазин --}}
        <section class="adm-settings-panel {{ $activeTab === 'shop' ? 'is-active' : '' }}" data-panel="shop">
            <header class="adm-settings-panel-head">
                <div>
                    <h2>Магазин і доставка</h2>
                    <p>Інформація на картці товару, каталог і відгуки</p>
                </div>
                <a href="{{ $tabs['shop']['preview'] }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">Переглянути</a>
            </header>

            <div class="adm-settings-card">
                <h3 class="adm-settings-card-title">Доставка та оплата</h3>
                <p class="adm-settings-card-lead">Блок «Довіра» на сторінці товару</p>
                <div class="admin-form-grid">
                    @include('partials.admin-settings-field', [
                        'name' => 'delivery_carriers',
                        'label' => 'Перевізники',
                        'hint' => 'Через кому — показується у футері',
                        'placeholder' => 'Nova Poshta, Ukrposhta, Meest',
                        'value' => $s['delivery_carriers'] ?? '',
                        'wide' => true,
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'shipping_info',
                        'label' => 'Текст про доставку',
                        'type' => 'textarea',
                        'rows' => 2,
                        'hint' => 'На сторінці товару',
                        'value' => $s['shipping_info'] ?? '',
                        'wide' => true,
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'returns_info',
                        'label' => 'Текст про повернення',
                        'type' => 'textarea',
                        'rows' => 2,
                        'value' => $s['returns_info'] ?? '',
                        'wide' => true,
                    ])
                    @include('partials.admin-settings-field', [
                        'name' => 'trust_payment_text',
                        'label' => 'Спосіб оплати',
                        'hint' => 'Короткий рядок біля іконки оплати',
                        'placeholder' => 'Онлайн або при отриманні',
                        'value' => $s['trust_payment_text'] ?? '',
                        'wide' => true,
                    ])
                </div>
            </div>

            <div class="adm-settings-card">
                <h3 class="adm-settings-card-title">Каталог і відгуки</h3>
                <div class="admin-form-grid">
                    @include('partials.admin-settings-field', [
                        'name' => 'new_products_days',
                        'label' => 'Скільки днів товар вважається «новинкою»',
                        'type' => 'number',
                        'min' => 1,
                        'max' => 365,
                        'hint' => 'Розділ /new показує товари за цей період',
                        'value' => $s['new_products_days'] ?? '30',
                    ])
                    @include('partials.admin-switch', [
                        'name' => 'reviews_moderation',
                        'label' => 'Модерація відгуків',
                        'hint' => 'Відгуки зʼявляться на сайті лише після схвалення в адмінці',
                        'checked' => old('reviews_moderation', $s['reviews_moderation'] ?? '0') === '1',
                    ])
                </div>
            </div>
        </section>

        <div class="adm-settings-savebar">
            <div class="adm-settings-savebar-copy">
                <strong>Збережи зміни</strong>
                <span>Налаштування оновляться на всьому сайті одразу після збереження</span>
            </div>
            <button type="submit" class="btn btn-dark">Зберегти налаштування</button>
        </div>
    </div>
</form>
@endsection

@section('admin_scripts')
<script>
(function () {
    var form = document.getElementById('admSettingsForm');
    var activeInput = document.getElementById('admSettingsActiveTab');
    var previewLead = document.getElementById('previewBrandLead');
    var previewAccent = document.getElementById('previewBrandAccent');
    var leadInput = document.getElementById('setting_brand_lead');
    var accentInput = document.getElementById('setting_brand_accent');

    function switchTab(name) {
        document.querySelectorAll('.adm-settings-nav-item').forEach(function (btn) {
            var active = btn.getAttribute('data-tab') === name;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        document.querySelectorAll('.adm-settings-panel').forEach(function (panel) {
            panel.classList.toggle('is-active', panel.getAttribute('data-panel') === name);
        });
        if (activeInput) activeInput.value = name;
        try { localStorage.setItem('admSettingsTab', name); } catch (e) {}
        if (history.replaceState) {
            history.replaceState(null, '', '?tab=' + encodeURIComponent(name));
        }
    }

    document.querySelectorAll('.adm-settings-nav-item').forEach(function (btn) {
        btn.addEventListener('click', function () {
            switchTab(btn.getAttribute('data-tab'));
        });
    });

    document.querySelectorAll('.adm-path-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var target = document.getElementById(chip.getAttribute('data-path-target'));
            if (target) {
                target.value = chip.getAttribute('data-path-value');
                target.focus();
            }
        });
    });

    function updateLogoPreview() {
        if (previewLead && leadInput) previewLead.textContent = leadInput.value || 'CLOTH';
        if (previewAccent && accentInput) previewAccent.textContent = accentInput.value || 'STORE';
    }

    if (leadInput) leadInput.addEventListener('input', updateLogoPreview);
    if (accentInput) accentInput.addEventListener('input', updateLogoPreview);

    var saved = null;
    try { saved = localStorage.getItem('admSettingsTab'); } catch (e) {}
    if (saved && document.querySelector('.adm-settings-nav-item[data-tab="' + saved + '"]') && !window.location.search.includes('tab=')) {
        switchTab(saved);
    }
})();
</script>
@endsection
