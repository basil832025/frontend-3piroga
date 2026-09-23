@php
    $footer_left_menu = \App\Support\Menus::bySlug('footer-left');
    $footer_center_menu = \App\Support\Menus::bySlug('footer-center');
    $footer_right_menu = \App\Support\Menus::bySlug('footer-right');


@endphp
<footer class="bg-white text-[#929292] xl:mt-[80px] mt-[40px]">
    <div class="max-w-screen-xl mx-auto px-4 md:px-6">
        {{-- линия-разделитель --}}
        <div class="border-t border-black/10"></div>

        {{-- сетка для md = 3 колонки, lg = 4 --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-x-10 gap-y-8 py-12">

            {{-- Три пирога --}}
            <div>
                <h3 class="text-lg font-semibold text-black"> {{ st('all.try-pyroha','Три пироги') }}</h3>
                @if($footer_left_menu)

                <ul class="mt-4 space-y-2 text-[14px] text-[#929292] font-bold">
                    @foreach ($footer_left_menu as $it)

                    <li><a class="hover:text-black" href="{{ $it['href'] ?: '#' }}">{{$it['label']}}</a></li>
                    @endforeach

                </ul>
                @endif
            </div>

            {{-- Юридична інформація --}}
            <div>
                <h3 class="text-lg font-semibold text-black">{{ st('all.iurydychna-informatsiia','Юридична інформація') }}</h3>
                @if($footer_center_menu)
                <ul class="mt-4 space-y-2 text-[14px] text-[#929292] font-bold">
                    @foreach ($footer_center_menu as $it)

                        <li><a class="hover:text-black" href="{{ $it['href'] ?: '#' }}">{{$it['label']}}</a></li>
                    @endforeach
                        </ul>
                @endif
            </div>

            {{-- Доставка і ресторани --}}
            <div>
                <h3 class="text-lg font-semibold text-black">{{ st('all.dostavka-and-restorany','Доставка і ресторани') }}</h3>
                @if($footer_center_menu)
                <ul class="mt-4 space-y-2 text-[14px] text-[#929292] font-bold">
                    @foreach ($footer_right_menu as $it)

                        <li><a class="hover:text-black" href="{{ $it['href'] ?: '#' }}">{{$it['label']}}</a></li>
                    @endforeach
                     </ul>
                @endif
            </div>

            {{-- Контакти --}}
            <div class="md:col-span-3 lg:col-span-1">
                <h3 class="text-lg font-semibold text-black">{{ st('all.contacts','Контакти') }}</h3>

                @php
                    // phones from HeaderContacts composer
                    $phones = collect($headerPhones ?? []);

                    // same working-hours block as in the burger menu
                    $pickup = ['time' => 'з 09:00 до 20:00', 'title' => 'Приймаємо замовлення на самовивіз'];
                    $delivery = ['time' => 'з 09:00 до 21:00', 'title' => 'Доставляємо замовлення'];

                    if (!empty($headerSchedule)) {
                        foreach ($headerSchedule as $schedule) {
                            $slug = trim((string) ($schedule['slug'] ?? ''));

                            if ($slug === 'delivery') {
                                $delivery['time'] = (string) ($schedule['time'] ?? $delivery['time']);
                                $delivery['title'] = (string) ($schedule['title'] ?? $delivery['title']);
                            }

                            if ($slug === 'pickup') {
                                $pickup['time'] = (string) ($schedule['time'] ?? $pickup['time']);
                                $pickup['title'] = (string) ($schedule['title'] ?? $pickup['title']);
                            }
                        }
                    }

                    // email/address — берем из location, если есть (подстрой ключи под свою модель Location)
                    $email   = data_get($headerLocation, 'email')
                            ?? data_get($headerLocation, 'contact_email')
                            ?? config('site.email', 'info@3piroga.ua');

                    $address = data_get($headerLocation, 'address')
                            ?? data_get($headerLocation, 'address_text')
                            ?? '';

                    $lat = data_get($headerLocation, 'lat');
                    $lng = data_get($headerLocation, 'lng');
                    $googleMapLink = (string) (data_get($headerLocation, 'google_map_link') ?? '');

                    if (is_numeric($lat) && is_numeric($lng)) {
                        $mapsHref = 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($lat . ',' . $lng);
                    } elseif ($googleMapLink !== '') {
                        $mapsHref = $googleMapLink;
                    } else {
                        $mapsHref = 'https://www.google.com/maps/search/?api=1&query=' . urlencode((string) $address);
                    }
                @endphp

                <div class="mt-4 flex flex-col gap-6 text-[14px] font-bold">
                    {{-- телефоны --}}
                    <ul class="space-y-2 text-[#272828]">
                        @foreach ($phones as $p)
                            <li>
                                <a href="tel:{{ $p['tel'] }}" class="hover:text-black">
                                    {{ $p['display'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="lg:hidden">
                        <h4 class="text-[16px] font-semibold text-black">{{ st('all.write-to-us-in-messengers', 'Напишіть нам у месенджери') }}</h4>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="https://t.me/OsetianBakery" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-2xl border border-[#E0E0E0] px-3 py-2 text-[14px] font-semibold text-[#272828] transition-colors hover:border-[#2AABEE]" aria-label="Написати в Telegram">
                                <svg class="h-9 w-9 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                                    <circle cx="24" cy="24" r="24" fill="#2AABEE"/>
                                    <path fill="#fff" d="M35.7 13.3 31.9 34c-.3 1.5-1.1 1.9-2.2 1.2l-5.8-4.3-2.8 2.7c-.3.3-.6.6-1.2.6l.4-5.9 10.8-9.8c.5-.4-.1-.7-.7-.3L17 26.6l-5.7-1.8c-1.2-.4-1.3-1.2.3-1.8l22.2-8.6c1-.4 1.9.2 1.9 1.1v-2.2z"/>
                                </svg>
                                <span>Telegram</span>
                            </a>

                            <a href="viber://chat?number=%2B380660784333" class="inline-flex items-center gap-2 rounded-2xl border border-[#E0E0E0] px-3 py-2 text-[14px] font-semibold text-[#272828] transition-colors hover:border-[#665CAC]" aria-label="Написати у Viber">
                                <svg class="h-9 w-9 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                                    <circle cx="24" cy="24" r="24" fill="#665CAC"/>
                                    <path fill="#fff" d="M24 9.5C15.7 9.5 10.5 13.4 10.5 21V26.5C10.5 32.4 14.1 36 19.4 37.2V41.2C19.4 41.8 20.1 42.1 20.6 41.7L25.3 37.7C32.8 37.5 37.5 33.8 37.5 26.3V21C37.5 13.4 32.3 9.5 24 9.5Z"/>
                                    <path fill="#665CAC" d="M18.1 16.8C17.5 16.8 16.8 17.3 16.5 17.8C15.8 19.2 16.1 21.7 17.4 24.3C19.2 27.8 22.1 30.6 25.6 32.3C28.1 33.5 30.6 33.8 32 33.1C32.6 32.8 33.1 32.1 33.1 31.5C33.1 31.1 32.8 30.7 32.4 30.5L29.4 28.7C28.9 28.4 28.3 28.5 27.9 28.9L26.7 30.1C26.5 30.3 26.2 30.3 25.9 30.2C22.5 28.7 20.2 26.4 18.7 23C18.6 22.7 18.6 22.4 18.8 22.2L20 21C20.4 20.6 20.5 20 20.2 19.5L18.8 17.2C18.6 16.9 18.4 16.8 18.1 16.8Z"/>
                                    <path d="M24.5 15.2C29 15.7 32.2 18.9 32.7 23.4M24.7 18.2C27.6 18.6 29.4 20.4 29.8 23.2" fill="none" stroke="#665CAC" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                <span>Viber</span>
                            </a>

                            <a href="https://wa.me/380660784333" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-2xl border border-[#E0E0E0] px-3 py-2 text-[14px] font-semibold text-[#272828] transition-colors hover:border-[#25D366]" aria-label="Написати у WhatsApp">
                                <svg class="h-9 w-9 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                                    <circle cx="24" cy="24" r="24" fill="#25D366"/>
                                    <path fill="#fff" d="M24 10.5C16.6 10.5 10.6 16.4 10.6 23.8C10.6 26.2 11.2 28.6 12.4 30.6L10.2 38.5L18.3 36.4C20.1 37.3 22 37.7 24 37.7C31.4 37.7 37.4 31.7 37.4 24.2C37.4 16.8 31.4 10.5 24 10.5ZM24 13.2C30 13.2 34.8 18.1 34.8 24.1C34.8 30.1 30 35 24 35C22.1 35 20.4 34.5 18.8 33.6L14.1 34.8L15.4 30.3C14.1 28.5 13.4 26.4 13.4 24.1C13.4 18.1 18.1 13.2 24 13.2Z"/>
                                    <path fill="#fff" d="M19.1 17.4C18.7 17.4 18.2 17.6 17.9 18C17.2 18.8 16.9 19.8 16.9 20.9C16.9 22 17.4 23.6 18.1 24.8C19.7 27.6 22.1 29.9 25 31.3C26.4 32 28.4 32.8 29.9 32.3C30.9 32 32.1 31 32.4 30C32.6 29.4 32.6 28.9 32.3 28.7C31.9 28.5 29.4 27.3 29 27.2C28.6 27 28.3 27 28 27.4L26.8 28.9C26.6 29.2 26.3 29.2 26 29.1C24.6 28.6 23.3 27.8 22.3 26.8C21.2 25.7 20.4 24.5 19.9 23.1C19.8 22.8 19.9 22.5 20.1 22.3L21 21.2C21.3 20.9 21.4 20.6 21.2 20.2L20 17.8C19.8 17.5 19.5 17.4 19.1 17.4Z"/>
                                </svg>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-[13px] font-semibold text-black">{{ __('location.sections.schedule') }}</h4>
                        <div>
                            <div class="text-[#9E9E9E] text-[13px]">{{ $pickup['title'] }}:</div>
                            <div class="font-semibold text-[14px] text-[#272828]">{{ $pickup['time'] }}</div>
                        </div>
                        <div>
                            <div class="text-[#9E9E9E] text-[13px]">{{ $delivery['title'] }}:</div>
                            <div class="font-semibold text-[14px] text-[#272828]">{{ $delivery['time'] }}</div>
                        </div>
                    </div>

                    {{-- email + address --}}
                    <ul class="space-y-2">
                        <li class="font-bold">
                            <a href="mailto:{{ $email }}" class="hover:text-black">{{ $email }}</a>
                        </li>

                        @if($address)
                            <li class="text-[#929292] font-normal">
                                <a href="{{ $mapsHref }}" target="_blank" rel="noopener noreferrer" class="hover:text-black">
                                    {{ $address }}
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>


        </div>

        {{-- Мессенджеры на десктопе: отдельная строка, чтобы кнопки не переносились в узкой колонке контактов --}}
        <div class="hidden lg:flex flex-col items-end gap-3 pb-8">
            <h3 class="whitespace-nowrap text-lg font-semibold text-black">{{ st('all.write-to-us-in-messengers', 'Напишіть нам у месенджери') }}</h3>
            <div class="grid grid-cols-3 gap-3">
                <a href="https://t.me/OsetianBakery" target="_blank" rel="noopener noreferrer" class="inline-flex min-w-[172px] items-center gap-3 rounded-2xl border border-[#E0E0E0] px-4 py-2.5 text-[15px] font-semibold text-[#272828] transition-colors hover:border-[#2AABEE]" aria-label="Написати в Telegram">
                    <svg class="h-10 w-10 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                        <circle cx="24" cy="24" r="24" fill="#2AABEE"/>
                        <path fill="#fff" d="M35.7 13.3 31.9 34c-.3 1.5-1.1 1.9-2.2 1.2l-5.8-4.3-2.8 2.7c-.3.3-.6.6-1.2.6l.4-5.9 10.8-9.8c.5-.4-.1-.7-.7-.3L17 26.6l-5.7-1.8c-1.2-.4-1.3-1.2.3-1.8l22.2-8.6c1-.4 1.9.2 1.9 1.1v-2.2z"/>
                    </svg>
                    <span>Telegram</span>
                </a>

                <a href="viber://chat?number=%2B380660784333" class="inline-flex min-w-[172px] items-center gap-3 rounded-2xl border border-[#E0E0E0] px-4 py-2.5 text-[15px] font-semibold text-[#272828] transition-colors hover:border-[#665CAC]" aria-label="Написати у Viber">
                    <svg class="h-10 w-10 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                        <circle cx="24" cy="24" r="24" fill="#665CAC"/>
                        <path fill="#fff" d="M24 9.5C15.7 9.5 10.5 13.4 10.5 21V26.5C10.5 32.4 14.1 36 19.4 37.2V41.2C19.4 41.8 20.1 42.1 20.6 41.7L25.3 37.7C32.8 37.5 37.5 33.8 37.5 26.3V21C37.5 13.4 32.3 9.5 24 9.5Z"/>
                        <path fill="#665CAC" d="M18.1 16.8C17.5 16.8 16.8 17.3 16.5 17.8C15.8 19.2 16.1 21.7 17.4 24.3C19.2 27.8 22.1 30.6 25.6 32.3C28.1 33.5 30.6 33.8 32 33.1C32.6 32.8 33.1 32.1 33.1 31.5C33.1 31.1 32.8 30.7 32.4 30.5L29.4 28.7C28.9 28.4 28.3 28.5 27.9 28.9L26.7 30.1C26.5 30.3 26.2 30.3 25.9 30.2C22.5 28.7 20.2 26.4 18.7 23C18.6 22.7 18.6 22.4 18.8 22.2L20 21C20.4 20.6 20.5 20 20.2 19.5L18.8 17.2C18.6 16.9 18.4 16.8 18.1 16.8Z"/>
                        <path d="M24.5 15.2C29 15.7 32.2 18.9 32.7 23.4M24.7 18.2C27.6 18.6 29.4 20.4 29.8 23.2" fill="none" stroke="#665CAC" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <span>Viber</span>
                </a>

                <a href="https://wa.me/380660784333" target="_blank" rel="noopener noreferrer" class="inline-flex min-w-[172px] items-center gap-3 rounded-2xl border border-[#E0E0E0] px-4 py-2.5 text-[15px] font-semibold text-[#272828] transition-colors hover:border-[#25D366]" aria-label="Написати у WhatsApp">
                    <svg class="h-10 w-10 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                        <circle cx="24" cy="24" r="24" fill="#25D366"/>
                        <path fill="#fff" d="M24 10.5C16.6 10.5 10.6 16.4 10.6 23.8C10.6 26.2 11.2 28.6 12.4 30.6L10.2 38.5L18.3 36.4C20.1 37.3 22 37.7 24 37.7C31.4 37.7 37.4 31.7 37.4 24.2C37.4 16.8 31.4 10.5 24 10.5ZM24 13.2C30 13.2 34.8 18.1 34.8 24.1C34.8 30.1 30 35 24 35C22.1 35 20.4 34.5 18.8 33.6L14.1 34.8L15.4 30.3C14.1 28.5 13.4 26.4 13.4 24.1C13.4 18.1 18.1 13.2 24 13.2Z"/>
                        <path fill="#fff" d="M19.1 17.4C18.7 17.4 18.2 17.6 17.9 18C17.2 18.8 16.9 19.8 16.9 20.9C16.9 22 17.4 23.6 18.1 24.8C19.7 27.6 22.1 29.9 25 31.3C26.4 32 28.4 32.8 29.9 32.3C30.9 32 32.1 31 32.4 30C32.6 29.4 32.6 28.9 32.3 28.7C31.9 28.5 29.4 27.3 29 27.2C28.6 27 28.3 27 28 27.4L26.8 28.9C26.6 29.2 26.3 29.2 26 29.1C24.6 28.6 23.3 27.8 22.3 26.8C21.2 25.7 20.4 24.5 19.9 23.1C19.8 22.8 19.9 22.5 20.1 22.3L21 21.2C21.3 20.9 21.4 20.6 21.2 20.2L20 17.8C19.8 17.5 19.5 17.4 19.1 17.4Z"/>
                    </svg>
                    <span>WhatsApp</span>
                </a>
            </div>
        </div>

            {{-- линия-разделитель --}}
            <div class="border-t border-black/10"></div>

            {{-- строка: логотип+лозунг слева | соцсети справа --}}
            <div class="py-8 flex flex-col gap-6 md:flex-row md:items-center justify-between">
                <div class=" flex items-start gap-4 desk:w-[498px] md:w-[419px]">
                    <img src="/vendor/frontend-3piroga/images/logo-footer.png" alt="Три Пироги" class="w-14 h-14">
                    <p class="text-[#666666] text-[13px] leading-snug">
                        Мережа пекарень "ТРИ ПИРОГИ"  <span class="emoji">&#x2668;&#xFE0F;&#x2668;&#xFE0F;&#x2668;&#xFE0F;</span> - єдина у Києві, де печуть справжні осетинські пироги з пилу, з жару, в дров`яній печі
                    </p>
                </div>

                <ul class="flex items-center gap-8 md:gap-5 md:justify-end">
                    <li><a href="https://www.facebook.com/3piroga.ua" target="_blank" aria-label="Facebook" class="text-black hover:text-[#FF7500]">
                            <x-icons.facebook class="w-6 h-6"/>
                        </a></li>
                    <li><a href="https://www.instagram.com/3piroga_ua" target="_blank" aria-label="Instagram" class="text-black hover:text-[#FF7500]">
                            <x-icons.instagram class="w-6 h-6"/>
                        </a></li>
                    <li><a href="https://www.tiktok.com/@tripiroga?_r=1" aria-label="TikTok" target="_blank" class="text-black hover:text-[#FF7500]">
                            <x-icons.tiktok class="w-6 h-6"/>
                        </a></li>
                    <li><a href="https://t.me/OsetianBakery" target="_blank" aria-label="Telegram" class="text-black hover:text-[#FF7500]">
                            <x-icons.telegram class="w-6 h-6"/>
                        </a></li>
                    <li><a href="viber://chat?number=%2B380660784333" aria-label="Viber" class="text-black hover:text-[#FF7500]">
                            <x-icons.viber class="w-6 h-6" style="transform: scale(1.15); transform-origin: center;"/>
                        </a></li>
                    <li><a href="https://www.youtube.com/channel/UC37VV_ZFmkTacWeHKFsYWLQ" target="_blank" aria-label="YouTube" class="text-black hover:text-[#FF7500]">
                            <x-icons.youtube class="w-6 h-6"/>
                        </a></li>
                </ul>
            </div>

            {{-- нижняя строка копирайта --}}
            <div class="border-t border-black/10"></div>
            <div class="py-6 flex flex-col md:flex-row md:items-center md:justify-left gap-4">
                <p class="text-[14px] text-[#A9A9A9]">© ТРИ ПИРОГИ. Усі права захищені</p>
                <div class="flex items-center gap-6">
                    <img src="/vendor/frontend-3piroga/images/payments/mastercard.png" alt="Mastercard" class="h-5">
                    <img src="/vendor/frontend-3piroga/images/payments/visa.png" alt="VISA" class="h-5">
                </div>
            </div>

        </div>
    </footer>
