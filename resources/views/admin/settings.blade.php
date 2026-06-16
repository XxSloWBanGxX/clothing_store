@extends('admin.layout')

@section('title', 'Налаштування сайту')

@section('admin_content')
@include('partials.admin-flash')

<form action="{{ url('/admin/settings') }}" method="POST" class="adm-form adm-settings-form">
    @csrf

    <div class="adm-settings-tabs" role="tablist">
        <button type="button" class="adm-settings-tab is-active" data-tab="general">Загальні</button>
        <button type="button" class="adm-settings-tab" data-tab="homepage">Головна</button>
        <button type="button" class="adm-settings-tab" data-tab="footer">Футер і доставка</button>
        <button type="button" class="adm-settings-tab" data-tab="catalog">Каталог</button>
    </div>

    <section class="adm-panel adm-settings-panel is-active" data-panel="general">
        <div class="adm-panel-head"><div><h2>Бренд і контакти</h2><p>Логотип, email, телефон, соцмережі</p></div></div>
        <div class="admin-form-grid">
            <div class="form-group">
                <label for="brand_lead">Логотип (основа)</label>
                <input type="text" id="brand_lead" name="brand_lead" value="{{ old('brand_lead', $settings['brand_lead'] ?? '') }}">
            </div>
            <div class="form-group">
                <label for="brand_accent">Логотип (акцент)</label>
                <input type="text" id="brand_accent" name="brand_accent" value="{{ old('brand_accent', $settings['brand_accent'] ?? '') }}">
            </div>
            <div class="form-group">
                <label for="brand_name">Назва бренду (футер)</label>
                <input type="text" id="brand_name" name="brand_name" value="{{ old('brand_name', $settings['brand_name'] ?? '') }}">
            </div>
            <div class="form-group">
                <label for="contact_email">Email</label>
                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}">
            </div>
            <div class="form-group">
                <label for="contact_phone">Телефон</label>
                <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}">
            </div>
            <div class="form-group">
                <label for="contact_location">Локація</label>
                <input type="text" id="contact_location" name="contact_location" value="{{ old('contact_location', $settings['contact_location'] ?? '') }}">
            </div>
            <div class="form-group">
                <label for="instagram_url">Instagram URL</label>
                <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}">
            </div>
            <div class="form-group">
                <label for="instagram_handle">Instagram @</label>
                <input type="text" id="instagram_handle" name="instagram_handle" value="{{ old('instagram_handle', $settings['instagram_handle'] ?? '') }}">
            </div>
        </div>
    </section>

    <section class="adm-panel adm-settings-panel" data-panel="homepage">
        <div class="adm-panel-head"><div><h2>Hero-блок</h2><p>Головний екран на домашній сторінці</p></div></div>
        <div class="admin-form-grid">
            <div class="form-group"><label for="hero_badge">Бейдж</label><input type="text" id="hero_badge" name="hero_badge" value="{{ old('hero_badge', $settings['hero_badge'] ?? '') }}"></div>
            <div class="form-group adm-grid-full"><label for="hero_title">Заголовок</label><textarea id="hero_title" name="hero_title" rows="2">{{ old('hero_title', $settings['hero_title'] ?? '') }}</textarea></div>
            <div class="form-group adm-grid-full"><label for="hero_text">Текст</label><textarea id="hero_text" name="hero_text" rows="3">{{ old('hero_text', $settings['hero_text'] ?? '') }}</textarea></div>
            <div class="form-group"><label for="hero_btn1_text">Кнопка 1</label><input type="text" id="hero_btn1_text" name="hero_btn1_text" value="{{ old('hero_btn1_text', $settings['hero_btn1_text'] ?? '') }}"></div>
            <div class="form-group"><label for="hero_btn1_url">Посилання 1</label><input type="text" id="hero_btn1_url" name="hero_btn1_url" value="{{ old('hero_btn1_url', $settings['hero_btn1_url'] ?? '') }}"></div>
            <div class="form-group"><label for="hero_btn2_text">Кнопка 2</label><input type="text" id="hero_btn2_text" name="hero_btn2_text" value="{{ old('hero_btn2_text', $settings['hero_btn2_text'] ?? '') }}"></div>
            <div class="form-group"><label for="hero_btn2_url">Посилання 2</label><input type="text" id="hero_btn2_url" name="hero_btn2_url" value="{{ old('hero_btn2_url', $settings['hero_btn2_url'] ?? '') }}"></div>
            <div class="form-group"><label for="hero_stat1_value">Стат 1 — число</label><input type="text" id="hero_stat1_value" name="hero_stat1_value" value="{{ old('hero_stat1_value', $settings['hero_stat1_value'] ?? '') }}"></div>
            <div class="form-group"><label for="hero_stat1_label">Стат 1 — підпис</label><input type="text" id="hero_stat1_label" name="hero_stat1_label" value="{{ old('hero_stat1_label', $settings['hero_stat1_label'] ?? '') }}"></div>
            <div class="form-group"><label for="hero_stat2_value">Стат 2 — число</label><input type="text" id="hero_stat2_value" name="hero_stat2_value" value="{{ old('hero_stat2_value', $settings['hero_stat2_value'] ?? '') }}"></div>
            <div class="form-group"><label for="hero_stat2_label">Стат 2 — підпис</label><input type="text" id="hero_stat2_label" name="hero_stat2_label" value="{{ old('hero_stat2_label', $settings['hero_stat2_label'] ?? '') }}"></div>
            <div class="form-group"><label for="hero_stat3_value">Стат 3 — число</label><input type="text" id="hero_stat3_value" name="hero_stat3_value" value="{{ old('hero_stat3_value', $settings['hero_stat3_value'] ?? '') }}"></div>
            <div class="form-group"><label for="hero_stat3_label">Стат 3 — підпис</label><input type="text" id="hero_stat3_label" name="hero_stat3_label" value="{{ old('hero_stat3_label', $settings['hero_stat3_label'] ?? '') }}"></div>
        </div>

        <div class="adm-panel-head adm-panel-head--sub"><div><h2>Переваги</h2></div></div>
        <div class="admin-form-grid">
            @for ($i = 1; $i <= 3; $i++)
                <div class="form-group"><label for="feature{{ $i }}_title">Блок {{ $i }} — заголовок</label><input type="text" id="feature{{ $i }}_title" name="feature{{ $i }}_title" value="{{ old('feature'.$i.'_title', $settings['feature'.$i.'_title'] ?? '') }}"></div>
                <div class="form-group adm-grid-full"><label for="feature{{ $i }}_text">Блок {{ $i }} — текст</label><textarea id="feature{{ $i }}_text" name="feature{{ $i }}_text" rows="2">{{ old('feature'.$i.'_text', $settings['feature'.$i.'_text'] ?? '') }}</textarea></div>
            @endfor
        </div>

        <div class="adm-panel-head adm-panel-head--sub"><div><h2>Банер внизу</h2></div></div>
        <div class="admin-form-grid">
            <div class="form-group"><label for="banner_label">Бейдж</label><input type="text" id="banner_label" name="banner_label" value="{{ old('banner_label', $settings['banner_label'] ?? '') }}"></div>
            <div class="form-group adm-grid-full"><label for="banner_title">Заголовок</label><input type="text" id="banner_title" name="banner_title" value="{{ old('banner_title', $settings['banner_title'] ?? '') }}"></div>
            <div class="form-group adm-grid-full"><label for="banner_text">Текст</label><textarea id="banner_text" name="banner_text" rows="2">{{ old('banner_text', $settings['banner_text'] ?? '') }}</textarea></div>
            <div class="form-group"><label for="banner_btn_text">Кнопка</label><input type="text" id="banner_btn_text" name="banner_btn_text" value="{{ old('banner_btn_text', $settings['banner_btn_text'] ?? '') }}"></div>
            <div class="form-group"><label for="banner_btn_url">Посилання</label><input type="text" id="banner_btn_url" name="banner_btn_url" value="{{ old('banner_btn_url', $settings['banner_btn_url'] ?? '') }}"></div>
        </div>
    </section>

    <section class="adm-panel adm-settings-panel" data-panel="footer">
        <div class="adm-panel-head"><div><h2>Футер</h2></div></div>
        <div class="admin-form-grid">
            <div class="form-group adm-grid-full"><label for="footer_description">Опис бренду</label><textarea id="footer_description" name="footer_description" rows="3">{{ old('footer_description', $settings['footer_description'] ?? '') }}</textarea></div>
            <div class="form-group"><label for="footer_strip_text">Смуга зверху — текст</label><input type="text" id="footer_strip_text" name="footer_strip_text" value="{{ old('footer_strip_text', $settings['footer_strip_text'] ?? '') }}"></div>
            <div class="form-group"><label for="footer_strip_link_text">Смуга — посилання</label><input type="text" id="footer_strip_link_text" name="footer_strip_link_text" value="{{ old('footer_strip_link_text', $settings['footer_strip_link_text'] ?? '') }}"></div>
            <div class="form-group"><label for="footer_strip_link_url">URL смуги</label><input type="text" id="footer_strip_link_url" name="footer_strip_link_url" value="{{ old('footer_strip_link_url', $settings['footer_strip_link_url'] ?? '') }}"></div>
            <div class="form-group adm-grid-full"><label for="delivery_carriers">Перевізники (через кому)</label><input type="text" id="delivery_carriers" name="delivery_carriers" value="{{ old('delivery_carriers', $settings['delivery_carriers'] ?? '') }}"></div>
            <div class="form-group adm-grid-full"><label for="shipping_info">Текст доставки (картка товару)</label><textarea id="shipping_info" name="shipping_info" rows="2">{{ old('shipping_info', $settings['shipping_info'] ?? '') }}</textarea></div>
            <div class="form-group adm-grid-full"><label for="returns_info">Текст повернення</label><textarea id="returns_info" name="returns_info" rows="2">{{ old('returns_info', $settings['returns_info'] ?? '') }}</textarea></div>
            <div class="form-group adm-grid-full"><label for="trust_payment_text">Текст оплати</label><input type="text" id="trust_payment_text" name="trust_payment_text" value="{{ old('trust_payment_text', $settings['trust_payment_text'] ?? '') }}"></div>
        </div>
    </section>

    <section class="adm-panel adm-settings-panel" data-panel="catalog">
        <div class="adm-panel-head"><div><h2>Каталог і відгуки</h2></div></div>
        <div class="admin-form-grid">
            <div class="form-group">
                <label for="new_products_days">«Новинки» — скільки днів</label>
                <input type="number" min="1" max="365" id="new_products_days" name="new_products_days" value="{{ old('new_products_days', $settings['new_products_days'] ?? '30') }}">
            </div>
            <div class="form-group adm-checkbox-row">
                <label class="adm-check">
                    <input type="checkbox" name="reviews_moderation" value="1" {{ old('reviews_moderation', $settings['reviews_moderation'] ?? '0') === '1' ? 'checked' : '' }}>
                    <span>Модерація відгуків перед публікацією</span>
                </label>
            </div>
        </div>
    </section>

    <div class="admin-form-actions adm-settings-actions">
        <button type="submit" class="btn btn-dark">Зберегти налаштування</button>
    </div>
</form>
@endsection

@section('admin_scripts')
<script>
document.querySelectorAll('.adm-settings-tab').forEach(function (tab) {
    tab.addEventListener('click', function () {
        var name = tab.getAttribute('data-tab');
        document.querySelectorAll('.adm-settings-tab').forEach(function (t) { t.classList.remove('is-active'); });
        document.querySelectorAll('.adm-settings-panel').forEach(function (p) { p.classList.remove('is-active'); });
        tab.classList.add('is-active');
        var panel = document.querySelector('.adm-settings-panel[data-panel="' + name + '"]');
        if (panel) panel.classList.add('is-active');
    });
});
</script>
@endsection
