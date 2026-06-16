@extends('admin.layout')

@section('title', 'Розсилка')

@section('admin_content')
@include('partials.admin-flash')

<section class="adm-kpi-grid adm-kpi-grid--3">
    <article class="adm-kpi">
        <span class="adm-kpi-label">Активних підписників</span>
        <strong class="adm-kpi-value">{{ $activeCount }}</strong>
    </article>
    <article class="adm-kpi">
        <span class="adm-kpi-label">Усього записів</span>
        <strong class="adm-kpi-value">{{ $subscribers->count() }}</strong>
    </article>
</section>

<section class="adm-panel">
    <div class="adm-panel-head">
        <div>
            <h2>Підписники newsletter</h2>
            <p>Email-и з форми підписки у футері сайту</p>
        </div>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Підписано</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subscribers as $sub)
                    @php
                        $isActive = isset($sub->active) ? (bool) $sub->active : empty($sub->unsubscribed_at ?? null);
                        $subscribedAt = $sub->created_at ?? $sub->subscribed_at ?? '—';
                    @endphp
                    <tr>
                        <td><strong>{{ $sub->email ?? '—' }}</strong></td>
                        <td class="adm-cell-muted">{{ $sub->phone ?? '—' }}</td>
                        <td class="adm-cell-muted">{{ $subscribedAt }}</td>
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
                                        <button type="submit" class="btn btn-light btn-sm">Відписати</button>
                                    </form>
                                @endif
                                <form action="{{ url('/admin/newsletter/' . $sub->id) }}" method="POST" onsubmit="return confirm('Видалити запис?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm">Видалити</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Підписників поки немає.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
