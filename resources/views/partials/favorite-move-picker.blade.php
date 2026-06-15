@php
    $targetFolders = $targetFolders ?? [];
@endphp
@if (! empty($targetFolders))
    <div class="fav-move-picker" data-fav-picker>
        <button type="button" class="fav-move-trigger fav-picker-trigger" aria-expanded="false" aria-haspopup="true">
            <span class="fav-move-trigger-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/><path d="M12 11v6M9 14l3 3 3-3"/></svg>
            </span>
            <span class="fav-move-trigger-text">Перемістити в інший список</span>
            <span class="fav-move-trigger-chevron" aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </span>
        </button>
        <div class="fav-picker-menu fav-move-menu" hidden>
            <p class="fav-picker-title">Куди перемістити</p>
            @foreach ($targetFolders as $folderName)
                <form action="{{ url('/favorites/move') }}" method="POST" class="fav-picker-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ (int) $productId }}">
                    <input type="hidden" name="from_folder" value="{{ $fromFolder }}">
                    <input type="hidden" name="to_folder" value="{{ $folderName }}">
                    <button type="submit" class="fav-picker-option fav-move-option">
                        <span class="fav-move-option-icon" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                        </span>
                        <span>{{ $folderName }}</span>
                    </button>
                </form>
            @endforeach
        </div>
    </div>
@endif
