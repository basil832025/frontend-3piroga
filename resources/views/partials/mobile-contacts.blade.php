<dialog id="mobile-contact-dialog" class="mobile-contact-dialog" aria-labelledby="mobile-contact-title" aria-describedby="mobile-contact-hint"
        x-data @click="if ($event.target === $el) { const r = $el.getBoundingClientRect(); if ($event.clientX < r.left || $event.clientX > r.right || $event.clientY < r.top || $event.clientY > r.bottom) $el.close() }"
        @resize.window="if (window.innerWidth >= 768 && $el.open) $el.close()">
    <button type="button" class="mobile-contact-close" @click="$el.closest('dialog').close()" aria-label="{{ $contactLabels['close'] }}" autofocus>×</button>
    <div class="mobile-contact-heading">
        <span class="mobile-contact-badge">@include(front_view('partials.contact-phone-icon'))</span>
        <div>
            <h2 id="mobile-contact-title">{{ $contactLabels['title'] }}</h2>
            <p id="mobile-contact-hint">{{ $contactLabels['hint'] }}</p>
        </div>
    </div>
    <ul class="mobile-contact-phones">
        @foreach(($headerPhones ?? []) as $phone)
            <li>
                <a href="tel:{{ preg_replace('/[^\d+]/', '', (string) ($phone['tel'] ?? '')) }}">
                    @include(front_view('partials.contact-phone-icon'))
                    <span>{{ $phone['display'] ?? $phone['tel'] ?? '' }}</span>
                    <span class="mobile-contact-call">@include(front_view('partials.contact-phone-icon'))</span>
                </a>
            </li>
        @endforeach
    </ul>
    <div class="mobile-contact-messengers">
        @include(front_view('partials.contact-messengers'))
    </div>
</dialog>
