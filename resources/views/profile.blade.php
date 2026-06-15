@extends('layouts.app')

@section('title', 'Особистий кабінет - CLOTHSTORE')

@section('content')

@php
    $orderStatusLabels = [
        'new' => 'Нове',
        'processing' => 'В обробці',
        'sent' => 'Відправлено',
        'completed' => 'Завершено',
        'cancelled' => 'Скасовано',
    ];
@endphp

<main class="staff-profile-page">
    <section class="staff-profile-top">
        <div class="container staff-profile-top-inner">
            <div>
                <span class="hero-badge">PROFILE</span>
                <h1>Особистий кабінет</h1>
                <p class="staff-profile-greeting">Привіт, {{ $user->name }}! Керуй даними, замовленнями та бонусами в одному місці.</p>
            </div>
        </div>
    </section>

    <section class="staff-profile-section">
        <div class="container staff-profile-shell">
            <aside class="staff-profile-sidebar">
                <nav class="staff-profile-nav" aria-label="Розділи профілю">
                    <a href="{{ url('/profile?tab=settings') }}" class="staff-profile-nav-link {{ $activeTab === 'settings' ? 'active' : '' }}">
                        <span class="staff-nav-icon">⚙</span>
                        Дані та налаштування
                    </a>
                    <a href="{{ url('/profile?tab=orders') }}" class="staff-profile-nav-link {{ $activeTab === 'orders' ? 'active' : '' }}">
                        <span class="staff-nav-icon">🛍</span>
                        Історія покупок
                    </a>
                    <a href="{{ url('/profile?tab=promos') }}" class="staff-profile-nav-link {{ $activeTab === 'promos' ? 'active' : '' }}">
                        <span class="staff-nav-icon">🏷</span>
                        Мої промокоди
                    </a>
                    <a href="{{ url('/profile?tab=bonus') }}" class="staff-profile-nav-link {{ $activeTab === 'bonus' ? 'active' : '' }}">
                        <span class="staff-nav-icon">★</span>
                        Історія бонусів
                    </a>
                    <a href="{{ url('/profile?tab=reviews') }}" class="staff-profile-nav-link {{ $activeTab === 'reviews' ? 'active' : '' }}">
                        <span class="staff-nav-icon">💬</span>
                        Історія відгуків
                    </a>
                </nav>

                <div class="staff-profile-sidebar-footer">
                    <form action="{{ url('/logout') }}" method="POST" class="staff-logout-form">
                        @csrf
                        <button type="submit" class="staff-logout-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Вийти з акаунту
                        </button>
                    </form>
                </div>
            </aside>

            <div class="staff-profile-content">
                {{-- ===== НАЛАШТУВАННЯ ===== --}}
                @if ($activeTab === 'settings')
                    <div class="staff-panel-head">
                        <div>
                            <h2>Дані та налаштування</h2>
                            <p class="staff-panel-sub">Профіль, пароль і адреса доставки</p>
                        </div>
                        <button type="button" class="staff-edit-btn" id="editProfileBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Редагувати дані
                        </button>
                    </div>

                    @if (session('profileSuccess'))<div class="alert-success">{{ session('profileSuccess') }}</div>@endif
                    @if (session('passwordSuccess'))<div class="alert-success">{{ session('passwordSuccess') }}</div>@endif
                    @if (session('avatarSuccess'))<div class="alert-success">{{ session('avatarSuccess') }}</div>@endif
                    @if (session('deliverySuccess'))<div class="alert-success">{{ session('deliverySuccess') }}</div>@endif

                    <div class="staff-settings-stack">
                        <div class="staff-profile-card">
                            <form action="{{ url('/profile/avatar') }}" method="POST" enctype="multipart/form-data" class="staff-avatar-form" id="avatarForm">
                                @csrf
                                <div class="staff-avatar-block">
                                    <div class="staff-avatar-wrap">
                                        <div class="staff-avatar" id="avatarPreview">
                                            @if (! empty($user->avatar))
                                                <img src="{{ asset('assets/images/avatars/' . $user->avatar) }}" alt="">
                                            @else
                                                <span>{{ mb_strtoupper(mb_substr($user->username ?? $user->name ?? 'U', 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <label for="staffAvatarInput" class="staff-avatar-edit" title="Обрати нове фото">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                        </label>
                                        <input type="file" name="avatar" id="staffAvatarInput" accept=".jpg,.jpeg,.png,.webp" hidden>
                                    </div>
                                    <div class="staff-avatar-meta">
                                        <strong>{{ $user->name }}</strong>
                                        <span>{{ '@' . $user->username }}</span>
                                        <p class="staff-avatar-hint" id="avatarFileHint">Натисни на камеру або обери файл (JPG, PNG до 4 МБ)</p>
                                    </div>
                                    <button type="submit" class="btn btn-dark staff-avatar-save" id="avatarSaveBtn" disabled>Зберегти фото</button>
                                </div>
                            </form>

                            <div class="staff-profile-divider"></div>

                            <div id="profileViewMode" class="staff-fields-grid">
                                <div class="staff-field">
                                    <label>Імʼя</label>
                                    <div class="staff-field-value">{{ $user->name }}</div>
                                </div>
                                <div class="staff-field">
                                    <label>Телефон</label>
                                    <div class="staff-field-value">{{ $user->phone ?: '—' }}</div>
                                </div>
                                <div class="staff-field">
                                    <label>Email</label>
                                    <div class="staff-field-value">{{ $user->email }}</div>
                                </div>
                                <div class="staff-field">
                                    <label>Username</label>
                                    <div class="staff-field-value">{{ $user->username }}</div>
                                </div>
                            </div>

                            <form action="{{ url('/profile/update') }}" method="POST" id="profileEditMode" class="staff-edit-form" style="display:none;">
                                @csrf
                                <div class="staff-fields-grid">
                                    <div class="staff-field">
                                        <label for="name">Імʼя</label>
                                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                                        @error('name')<small class="form-error">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="staff-field">
                                        <label for="phone">Телефон</label>
                                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                        @error('phone')<small class="form-error">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="staff-field">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
                                        @error('email')<small class="form-error">{{ $message }}</small>@enderror
                                    </div>
                                    <div class="staff-field">
                                        <label for="username">Username</label>
                                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}">
                                        @error('username')<small class="form-error">{{ $message }}</small>@enderror
                                    </div>
                                </div>
                                <div class="staff-edit-actions">
                                    <button type="button" class="btn btn-light" id="cancelEditBtn">Скасувати</button>
                                    <button type="submit" class="btn btn-dark">Зберегти зміни</button>
                                </div>
                            </form>

                            <button type="button" class="staff-action-btn" id="openPasswordModal">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                Змінити пароль
                            </button>
                        </div>

                        <div class="staff-profile-card staff-delivery-card">
                            <header class="staff-card-head">
                                <span class="staff-card-icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                </span>
                                <div>
                                    <h3>Адреса доставки</h3>
                                    <p>Збережеться для швидкого оформлення замовлень</p>
                                </div>
                            </header>

                            <form action="{{ url('/profile/delivery') }}" method="POST" class="staff-delivery-form">
                                @csrf
                                @include('partials.delivery-picker', [
                                    'prefix' => 'profile',
                                    'saved' => $deliverySaved,
                                ])
                                @error('delivery_city')<small class="form-error">{{ $message }}</small>@enderror
                                @error('delivery_branch')<small class="form-error">{{ $message }}</small>@enderror
                                <button type="submit" class="btn btn-dark staff-delivery-save">Зберегти адресу</button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- ===== ІСТОРІЯ ПОКУПОК ===== --}}
                @if ($activeTab === 'orders')
                    <div class="staff-panel-head">
                        <div>
                            <h2>Історія покупок</h2>
                            <p class="staff-panel-sub">Усі твої замовлення в одному місці</p>
                        </div>
                    </div>

                    @if (session('orderSuccess'))<div class="alert-success">{{ session('orderSuccess') }}</div>@endif
                    @if (session('orderError'))<div class="alert-error">{{ session('orderError') }}</div>@endif

                    @if (count($orders) > 0)
                        <div class="staff-profile-card staff-orders-table">
                            @foreach ($orders as $order)
                                @php
                                    $canCancel = in_array($order->status, ['new', 'processing'], true);
                                    $statusLabel = $orderStatusLabels[$order->status] ?? $order->status;
                                @endphp
                                <div class="staff-order-row">
                                    <div class="staff-order-main">
                                        <strong>#{{ (int) $order->id }}</strong>
                                        <span>{{ $order->created_at }}</span>
                                    </div>
                                    <div class="staff-order-sum">{{ number_format((float) $order->total_amount, 0, '.', ' ') }} грн</div>
                                    <div class="staff-order-status {{ $order->status === 'cancelled' ? 'cancelled' : '' }}">{{ $statusLabel }}</div>
                                    <div class="staff-order-actions">
                                        <a href="{{ url('/profile/order/' . $order->id) }}" class="staff-mini-btn">Деталі</a>
                                        @if ($canCancel)
                                            <form action="{{ url('/profile/order/' . $order->id . '/cancel') }}" method="POST" onsubmit="return confirm('Скасувати замовлення #{{ (int) $order->id }}?');">
                                                @csrf
                                                <button type="submit" class="staff-mini-btn danger">Скасувати</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="staff-empty">
                            <h3>У тебе ще немає замовлень</h3>
                            <p>Оформи перше замовлення в каталозі.</p>
                            <a href="{{ url('/catalog') }}" class="btn btn-dark">Перейти в каталог</a>
                        </div>
                    @endif
                @endif

                {{-- ===== ПРОМОКОДИ ===== --}}
                @if ($activeTab === 'promos')
                    <div class="staff-panel-head">
                        <div>
                            <h2>Мої промокоди</h2>
                            <p class="staff-panel-sub">Активні знижки та використані коди</p>
                        </div>
                    </div>

                    @if (count($promocodes) > 0)
                        <div class="staff-promo-grid">
                            @foreach ($promocodes as $promo)
                                <div class="staff-promo-card {{ $promo->used_at ? 'used' : '' }}">
                                    <span class="staff-promo-code">{{ $promo->code }}</span>
                                    <strong>{{ $promo->title }}</strong>
                                    <p>Знижка {{ (int) $promo->discount_percent }}%</p>
                                    @if ($promo->expires_at)
                                        <small>Діє до {{ \Illuminate\Support\Carbon::parse($promo->expires_at)->format('d.m.Y') }}</small>
                                    @endif
                                    @if ($promo->used_at)
                                        <span class="staff-promo-badge">Використано</span>
                                    @else
                                        <span class="staff-promo-badge active">Активний</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="staff-empty">
                            <h3>Промокодів поки немає</h3>
                            <p>Слідкуй за акціями — промокоди зʼявляться тут.</p>
                        </div>
                    @endif
                @endif

                {{-- ===== БОНУСИ ===== --}}
                @if ($activeTab === 'bonus')
                    <div class="staff-panel-head">
                        <div>
                            <h2>Історія бонусів</h2>
                            <p class="staff-panel-sub">Нарахування та списання бонусів</p>
                        </div>
                    </div>

                    <div class="staff-bonus-balance">
                        <span>Твій баланс</span>
                        <strong>{{ (int) $bonusPoints }} бонусів</strong>
                        <p>1 бонус = 1 грн знижки при наступному замовленні</p>
                    </div>

                    @if (count($bonusHistory) > 0)
                        <div class="staff-profile-card staff-bonus-list">
                            @foreach ($bonusHistory as $item)
                                <div class="staff-bonus-row">
                                    <div>
                                        <strong>{{ $item->description }}</strong>
                                        <span>{{ $item->created_at }}</span>
                                    </div>
                                    <span class="{{ $item->points >= 0 ? 'plus' : 'minus' }}">
                                        {{ $item->points >= 0 ? '+' : '' }}{{ (int) $item->points }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="staff-empty">
                            <h3>Історія бонусів порожня</h3>
                            <p>Бонуси нараховуються за реєстрацію та покупки.</p>
                        </div>
                    @endif
                @endif

                {{-- ===== ВІДГУКИ ===== --}}
                @if ($activeTab === 'reviews')
                    <div class="staff-panel-head">
                        <div>
                            <h2>Історія відгуків</h2>
                            <p class="staff-panel-sub">Твої оцінки та коментарі до товарів</p>
                        </div>
                    </div>

                    @if (count($reviews) > 0)
                        <div class="staff-profile-card staff-reviews-list">
                            @foreach ($reviews as $review)
                                <div class="staff-review-row">
                                    <div>
                                        @if ($review->product_id)
                                            <a href="{{ url('/product/' . $review->product_id) }}" class="staff-review-product">{{ $review->product_name ?? 'Товар #' . $review->product_id }}</a>
                                        @else
                                            <strong>{{ $review->product_name ?? 'Товар' }}</strong>
                                        @endif
                                        <span class="staff-review-stars">{!! str_repeat('★', (int) $review->rating) . str_repeat('☆', 5 - (int) $review->rating) !!}</span>
                                        <p>{{ $review->comment }}</p>
                                        <small>{{ $review->created_at }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="staff-empty">
                            <h3>Ти ще не залишав відгуків</h3>
                            <p>Після покупки можеш оцінити товар на сторінці продукту.</p>
                            <a href="{{ url('/catalog') }}" class="btn btn-dark">До каталогу</a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </section>
</main>

<div class="staff-modal" id="passwordModal" aria-hidden="true">
    <div class="staff-modal-backdrop" id="passwordModalBackdrop"></div>
    <div class="staff-modal-box">
        <button type="button" class="staff-modal-close" id="closePasswordModal">&times;</button>
        <h3>Змінити пароль</h3>
        <p class="staff-modal-sub">Введи поточний пароль і новий — мінімум 6 символів</p>
        <form action="{{ url('/profile/password') }}" method="POST">
            @csrf
            <div class="staff-field">
                <label for="current_password">Поточний пароль</label>
                <input type="password" id="current_password" name="current_password">
                @error('current_password')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="staff-field">
                <label for="new_password">Новий пароль</label>
                <input type="password" id="new_password" name="new_password">
                @error('new_password')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <div class="staff-field">
                <label for="confirm_password">Підтверди пароль</label>
                <input type="password" id="confirm_password" name="confirm_password">
                @error('confirm_password')<small class="form-error">{{ $message }}</small>@enderror
            </div>
            <button type="submit" class="btn btn-dark staff-modal-submit">Оновити пароль</button>
        </form>
    </div>
</div>

<script src="{{ asset('assets/js/profile.js') }}"></script>
<script src="{{ asset('assets/js/delivery-picker.js') }}"></script>

@if ($errors->has('current_password') || $errors->has('new_password'))
<script>document.addEventListener('DOMContentLoaded', function () { document.getElementById('passwordModal')?.classList.add('open'); });</script>
@endif

@if ($errors->has('name') || $errors->has('phone') || $errors->has('email') || $errors->has('username'))
<script>document.addEventListener('DOMContentLoaded', function () {
    const viewMode = document.getElementById('profileViewMode');
    const editMode = document.getElementById('profileEditMode');
    const editBtn = document.getElementById('editProfileBtn');
    if (viewMode && editMode) { viewMode.style.display = 'none'; editMode.style.display = 'block'; editBtn?.classList.add('is-active'); }
});</script>
@endif

@endsection
