@if (session('status'))
    <div class="alert-success adm-alert">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="alert-error adm-alert">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif
