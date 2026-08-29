@php
    $score = max(0, min(5, (float) $value));
@endphp

<span
    class="rating-stars"
    role="img"
    aria-label="{{ number_format($score, 1) }} out of 5 stars"
    title="{{ number_format($score, 1) }} out of 5 stars">
    @for($star = 1; $star <= 5; $star++)
        @php
            $fill = max(0, min(1, $score - ($star - 1))) * 100;
        @endphp

        <span class="rating-star" aria-hidden="true">
            <i class="bi bi-star"></i>
            <span class="rating-star-fill" style="width: {{ $fill }}%">
                <i class="bi bi-star-fill"></i>
            </span>
        </span>
    @endfor
</span>
