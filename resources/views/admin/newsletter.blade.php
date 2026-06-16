@extends('admin.layout')

@section('title', 'Розсилка')

@section('admin_content')
@include('partials.admin-flash')

@php
    $stats = $stats ?? ['total' => 0, 'active' => 0, 'unsubscribed' => 0, 'recent' => 0];
    $filter = $filter ?? 'all';
    $search = $search ?? '';
    $filters = [
        'all' => 'Усі',
        'active' => 'Активні',
        'unsubscribed' => 'Відписані',
    ];
@endphp

<section class="adm-kpi-grid adm-kpi-grid--4">
    <article class="adm-kpi"><span class="adm-kpi-label">Усього записів</span><strong class="adm-kpi-value">{{ $stats['total'] }}</strong></article>
    <article class="adm-kpi adm-kpi--success"><span class="adm-kpi-label">Активних підписників</span><strong class="adm-kpi-value">{{ $stats['active'] }}</strong></article>
    <article class="adm-kpi adm-kpi--muted"><span class="adm-kpi-label">Відписались</span><strong class="adm-kpi-value">{{ $stats['unsubscribed'] }}</strong></article>
    <article class="adm-kpi adm-kpi--info"><span class="adm-kpi-label">Нових за 30 днів</span><strong class="adm-kpi-value">{{ $stats['recent'] }}</strong></article>
</section>

<div class="adm-marketing-shell adm-marketing-shell--single">
    <div class="adm-marketing-main adm-marketing-main--full">
        <header class="adm-marketing-head">
            <div>
                <h2>Email-розсилка</h2>
                <p>Підписники з форми у футері сайту — тут можна переглянути базу та керувати статусом</p>
            </div>
            <a href="{{ url('/') }}#footer" target="_blank" rel="noopener" class="btn btn-light btn-sm">Форма на сайті</a>
        </header>

        <div class="adm-settings-card adm-settings-card--info">
            <h3 class="adm-settings-card-title">Як це працює</h3>
            <ul class="adm-info-list">
                <li>Клієнт вводить email у блоці «Розсилка новинок» у футері</li>
                <li>Адреса зберігається тут — для кампаній або експорту</li>
                <li><strong>Відписати</strong> — позначає email як неактивний, але залишає запис</li>
                <li><strong>Видалити</strong> — повністю прибирає email з бази</li>
            </ul>
        </div>

        <div class="adm-settings-card">
            <h3 class="adm-settings-card-title">Надіслати кампанію</h3>
            <p class="adm-settings-card-desc">Наприклад «Нова колекція» — лист піде всім активним підписникам ({{ $stats['active'] }}).</p>
            <form action="{{ url('/admin/newsletter/send') }}" method="POST" class="adm-campaign-form">
                @csrf
                <div class="form-group">
                    <label for="campaign_subject">Тема листа</label>
                    <input type="text" id="campaign_subject" name="subject" value="{{ old('subject', 'Нова колекція — CLOTHSTORE') }}" required maxlength="200">
                </div>
                <div class="form-group">
                    <label for="campaign_body">Текст</label>
                    <textarea id="campaign_body" name="body" rows="6" required placeholder="Привіт! Ми щойно опублікували нову колекцію…">{{ old('body') }}</textarea>
                </div>
                @error('send')<p class="form-error">{{ $message }}</p>@enderror
                <button type="submit" class="btn btn-dark" onclick="return confirm('Надіслати розсилку {{ $stats['active'] }} підписникам?')">Надіслати розсилку</button>
            </form>
        </div>

        <div class="adm-marketing-toolbar">
            <form action="{{ url('/admin/newsletter') }}" method="GET" class="adm-search-form">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <input type="search" name="search" value="{{ $search }}" placeholder="Пошук за email…" class="adm-search-input">
                <button type="submit" class="btn btn-dark btn-sm">Знайти</button>
                @if ($search !== '')
                    <a href="{{ url('/admin/newsletter?filter=' . $filter) }}" class="btn btn-light btn-sm">Скинути</a>
                @endif
            </form>

            <div class="adm-filter-tabs">
                @foreach ($filters as $key => $label)
                    <a href="{{ url('/admin/newsletter?filter=' . $key . ($search !== '' ? '&search=' . urlencode($search) : '')) }}" class="adm-filter-tab {{ $filter === $key ? 'is-active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        @if ($filtered->isNotEmpty())
            <div class="adm-table-wrap">
                <table class="adm-table adm-table--comfortable">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Дата підписки</th>
                            <th>Статус</th>
                            <th>Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($filtered as $sub)
                            @php
                                $isActive = isset($sub->active) ? (bool) $sub->active : empty($sub->unsubscribed_at ?? null);
                                $subscribedAt = $sub->subscribed_at ?? $sub->created_at ?? null;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $sub->email ?? '—' }}</strong>
                                    <a href="mailto:{{ $sub->email }}" class="adm-table-link">Написати</a>
                                </td>
                                <td class="adm-cell-muted">
                                    @if ($subscribedAt)
                                        {{ \Illuminate\Support\Carbon::parse($subscribedAt)->format('d.m.Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if ($isActive)
                                        <span class="adm-badge adm-badge--success">Активний</span>
                                    @else
                                        <span class="adm-badge adm-badge--neutral">Відписаний</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="adm-row-actions">
                                        @if ($isActive)
                                            <form action="{{ url('/admin/newsletter/' . $sub->id . '/unsubscribe') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-light btn-sm" title="Позначити як відписаного">Відписати</button>
                                            </form>
                                        @endif
                                        <form action="{{ url('/admin/newsletter/' . $sub->id) }}" method="POST" onsubmit="return confirm('Видалити запис назавжди?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="adm-list-footnote">Показано {{ $filtered->count() }} з {{ $stats['total'] }} записів</p>
        @else
            <div class="adm-marketing-empty">
                @if ($search !== '')
                    <p>За запитом «{{ $search }}» нічого не знайдено.</p>
                    <a href="{{ url('/admin/newsletter') }}" class="btn btn-light btn-sm">Показати всіх</a>
                @elseif ($filter !== 'all')
                    <p>Немає підписників у цій категорії.</p>
                    <a href="{{ url('/admin/newsletter') }}" class="btn btn-light btn-sm">Показати всіх</a>
                @else
                    <p>Підписників поки немає.</p>
                    <p class="adm-marketing-empty-hint">Як тільки хтось підпишеться через футер сайту — email зʼявиться тут автоматично.</p>
                    <a href="{{ url('/') }}#footer" target="_blank" rel="noopener" class="btn btn-dark btn-sm">Переглянути форму</a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
