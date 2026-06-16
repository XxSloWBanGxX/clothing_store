@php
    $statusLabels = $statusLabels ?? \App\Http\Controllers\AdminController::orderStatusLabels();
    $tone = \App\Http\Controllers\AdminController::orderStatusTone($status);
    $label = $statusLabels[$status] ?? $status;
@endphp
<span class="adm-badge adm-badge--{{ $tone }}">{{ $label }}</span>
