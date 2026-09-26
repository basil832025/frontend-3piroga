@props([
'event' => 'open-mobile-menu',        // какое событие открывает
'side'  => 'left',                    // 'left' | 'right'
'width' => 'w-[304px] sm:w-[360px] lg:w-[420px] max-w-[90vw]',
'panelClass' => '',                   // доп. классы для панели
'overlayClass' => '',                 // доп. классы для затемнения
])
@php
                     if (! isset($headerLocation, $headerPhones, $headerPhonePrimary, $headerSchedule)) {
                         $header = app(\App\Services\HeaderContacts::class)->buildBySlug(config('site.header_location_slug', '3pie'));

                         $headerPhones = $headerPhones ?? ($header['phones'] ?? collect());
                         $headerPhonePrimary = $headerPhonePrimary ?? ($header['primary'] ?? null);
                         $headerLocation = $headerLocation ?? ($header['location'] ?? null);
                         $headerSchedule = $headerSchedule ?? ($header['schedule'] ?? collect());
                     }

                     $MainMenuItems = $MainMenuItems ?? [];

                     // phones from HeaderContacts composer
                     $phones = collect($headerPhones ?? []);
                     $phoneCols = $phones->chunk(2); // по 2 номера в колонку

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

                      $hasCoords = is_numeric($lat) && is_numeric($lng);

                      // Fallback link: destination only
                      if ($hasCoords) {
                          $destination = $lat . ',' . $lng;
                          $mapsHref = 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($destination);
                      } elseif ($googleMapLink !== '') {
                          $mapsHref = $googleMapLink;
                      } else {
                          $mapsHref = 'https://www.google.com/maps/search/?api=1&query=' . urlencode((string) $address);
                      }



       $burgerCatalogItems = collect($MainMenuItems)->map(function ($it) {
    return [
        'key'        => $it['slug'] ?? $it['id'],
        'label'      => $it['label'],
        'href'       => $it['url'],
        'activeWhen' => ltrim($it['url'], '/') . '*',
    ];
})->values()->all();




  $accountMenu = \App\Support\Menus::bySlug('profile-menu');
  $aboutMenu = \App\Support\Menus::bySlug('menu-left-pages');
@endphp
<div
    x-data="{ open: false }"
    x-on:{{ $event }}.window="open = true"
    x-init="$watch('open', v => {
        document.documentElement.classList.toggle('no-scroll', v)
        document.body.classList.toggle('no-scroll', v)
     })"
    x-on:keydown.window.escape="open = false"

    x-cloak
>
    <!-- затемнение -->
    <div
        x-show="open"
        x-transition.opacity
        @click="open=false"
        class="fixed inset-0 z-50 bg-black/40 {{ $overlayClass }}"
        aria-hidden="true"
    ></div>

@php
    $from = $side === 'right' ? 'translate-x-full' : '-translate-x-full';
    $pos  = $side === 'right' ? 'right-0' : 'left-0';
@endphp


    <!-- панель слева -->
    <aside
        x-show="open"
        x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        :id="$id('drawer')"
        class="fixed left-0 top-0 z-[60] h-full md:w-[414px] w-[355px] bg-white shadow-2xl h-full overflow-y-auto custom-scroll"
        role="dialog"
        aria-modal="true"
    >
        <!-- ВНУТРЕННИЙ СКРОЛЛЕР  class="h-full overflow-y-auto pr-3 custom-scroll" -->
        <div >
            <!-- Шапка меню -->
            <div class="flex items-center justify-between px-8 py-6">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('vendor/frontend-3piroga/images/logo.svg') }}" alt="Три пироги">

                    {{-- Language switch (как в хидере) --}}
                    <div class="ml-2">
                        <x-ui.lang-switch variant="burger" />
                    </div>
                </div>

                <button class="p-2 rounded-lg hover:bg-black/5" @click="open=false" aria-label="Закрыть меню">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 6l12 12M18 6l-12 12"/>
                    </svg>
                </button>
            </div>

        <!-- Контент (скролл внутри) -->
        <div class="h-[calc(100%-56px)] overflow-y-auto px-8">
            <!-- разделы -->
            <x-ui.menu-list  :items="$accountMenu" :is_account_menu="true"  />
            <x-ui.menu-list  :items="$aboutMenu" :remember="true" />


               <div class="mt-6">
                   <x-ui.menu-list
                       title="{{ st('menu.title', 'Меню') }}"
                       :items="$burgerCatalogItems"
                       :remember="true"
                   />

                   @guest('web')
            <!-- Кнопка входа -->
            <div class="px-3 py-5">
                <a href="{{ route('auth.show') }}"
                   class="block text-center rounded-[4px] bg-[#FF7500] text-white font-semibold py-3">
                    {{ st('auth.login','Увійти') }}
                </a>
            </div>
                   @endguest

               <!-- Контакты -->
            <div class="px-3 pb-6 text-[16px] text-[#272828]">
                <h4 class="font-semibold mb-2">{{ st('all.contacts','Контакти') }}</h4>
                <div class="space-y-1">

                    {{-- телефоны --}}
                    @foreach ($phoneCols as $col)
                            @foreach ($col as $p)
                                    <a href="tel:{{ $p['tel'] }}" class="hover:text-black block">
                                        {{ $p['display'] }}
                                    </a>
                            @endforeach
                    @endforeach

                    <a href="mailto:{{ $email }}" class="hover:text-black block">{{ $email }}</a>

                    <div class="pt-3">
                        <h4 class="text-[16px] font-semibold text-[#272828]">{{ st('all.write-to-us-in-messengers', 'Напишіть нам у месенджери') }}</h4>
                        <div class="-mx-1 mt-2 grid grid-cols-3 gap-1.5">
                            <a href="https://t.me/OsetianBakery" target="_blank" rel="noopener noreferrer" class="flex h-9 w-full min-w-0 items-center justify-center gap-1 rounded-2xl border border-[#E0E0E0] px-1 text-[10px] font-semibold text-[#272828] transition-colors hover:border-[#2AABEE]" aria-label="Написати в Telegram">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                                    <circle cx="24" cy="24" r="24" fill="#2AABEE"/>
                                    <path fill="#fff" d="M35.7 13.3 31.9 34c-.3 1.5-1.1 1.9-2.2 1.2l-5.8-4.3-2.8 2.7c-.3.3-.6.6-1.2.6l.4-5.9 10.8-9.8c.5-.4-.1-.7-.7-.3L17 26.6l-5.7-1.8c-1.2-.4-1.3-1.2.3-1.8l22.2-8.6c1-.4 1.9.2 1.9 1.1v-2.2z"/>
                                </svg>
                                <span class="whitespace-nowrap leading-none">Telegram</span>
                            </a>

                            <a href="viber://chat?number=%2B380660784333" class="flex h-9 w-full min-w-0 items-center justify-center gap-1 rounded-2xl border border-[#E0E0E0] px-1 text-[10px] font-semibold text-[#272828] transition-colors hover:border-[#665CAC]" aria-label="Написати у Viber">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                                    <circle cx="24" cy="24" r="24" fill="#665CAC"/>
                                    <path fill="#fff" d="M24 9.5C15.7 9.5 10.5 13.4 10.5 21V26.5C10.5 32.4 14.1 36 19.4 37.2V41.2C19.4 41.8 20.1 42.1 20.6 41.7L25.3 37.7C32.8 37.5 37.5 33.8 37.5 26.3V21C37.5 13.4 32.3 9.5 24 9.5Z"/>
                                    <path fill="#665CAC" d="M18.1 16.8C17.5 16.8 16.8 17.3 16.5 17.8C15.8 19.2 16.1 21.7 17.4 24.3C19.2 27.8 22.1 30.6 25.6 32.3C28.1 33.5 30.6 33.8 32 33.1C32.6 32.8 33.1 32.1 33.1 31.5C33.1 31.1 32.8 30.7 32.4 30.5L29.4 28.7C28.9 28.4 28.3 28.5 27.9 28.9L26.7 30.1C26.5 30.3 26.2 30.3 25.9 30.2C22.5 28.7 20.2 26.4 18.7 23C18.6 22.7 18.6 22.4 18.8 22.2L20 21C20.4 20.6 20.5 20 20.2 19.5L18.8 17.2C18.6 16.9 18.4 16.8 18.1 16.8Z"/>
                                    <path d="M24.5 15.2C29 15.7 32.2 18.9 32.7 23.4M24.7 18.2C27.6 18.6 29.4 20.4 29.8 23.2" fill="none" stroke="#665CAC" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                <span class="whitespace-nowrap leading-none">Viber</span>
                            </a>

                            <a href="https://wa.me/380660784333" target="_blank" rel="noopener noreferrer" class="flex h-9 w-full min-w-0 items-center justify-center gap-1 rounded-2xl border border-[#E0E0E0] px-1 text-[10px] font-semibold text-[#272828] transition-colors hover:border-[#25D366]" aria-label="Написати у WhatsApp">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 48 48" aria-hidden="true">
                                    <circle cx="24" cy="24" r="24" fill="#25D366"/>
                                    <path fill="#fff" d="M24 10.5C16.6 10.5 10.6 16.4 10.6 23.8C10.6 26.2 11.2 28.6 12.4 30.6L10.2 38.5L18.3 36.4C20.1 37.3 22 37.7 24 37.7C31.4 37.7 37.4 31.7 37.4 24.2C37.4 16.8 31.4 10.5 24 10.5ZM24 13.2C30 13.2 34.8 18.1 34.8 24.1C34.8 30.1 30 35 24 35C22.1 35 20.4 34.5 18.8 33.6L14.1 34.8L15.4 30.3C14.1 28.5 13.4 26.4 13.4 24.1C13.4 18.1 18.1 13.2 24 13.2Z"/>
                                    <path fill="#fff" d="M19.1 17.4C18.7 17.4 18.2 17.6 17.9 18C17.2 18.8 16.9 19.8 16.9 20.9C16.9 22 17.4 23.6 18.1 24.8C19.7 27.6 22.1 29.9 25 31.3C26.4 32 28.4 32.8 29.9 32.3C30.9 32 32.1 31 32.4 30C32.6 29.4 32.6 28.9 32.3 28.7C31.9 28.5 29.4 27.3 29 27.2C28.6 27 28.3 27 28 27.4L26.8 28.9C26.6 29.2 26.3 29.2 26 29.1C24.6 28.6 23.3 27.8 22.3 26.8C21.2 25.7 20.4 24.5 19.9 23.1C19.8 22.8 19.9 22.5 20.1 22.3L21 21.2C21.3 20.9 21.4 20.6 21.2 20.2L20 17.8C19.8 17.5 19.5 17.4 19.1 17.4Z"/>
                                </svg>
                                <span class="whitespace-nowrap leading-none">WhatsApp</span>
                            </a>
                        </div>
                    </div>

                      <div class="mt-2 text-xs text-[#929292]">
                          @if($address)
                                 <a
                                     href="{{ $mapsHref }}"
                                     class="hover:text-[#272828] underline underline-offset-2"
                                     target="_blank"
                                     rel="noopener noreferrer"
                                     data-maps-link="1"
                                     data-maps-destination="{{ $hasCoords ? ($lat . ',' . $lng) : '' }}"
                                     data-maps-destination-address="{{ e((string) $address) }}"
                                 >
                                     {{ $address }}
                                 </a>
                          @endif
                      </div>

                      @php
                          // Same schedule rendering as on /nashi-restorany
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
                      @endphp

                      <div class="mt-4">
                          <h4 class="font-semibold mb-2 text-[16px] text-[#272828]">{{ __('location.sections.schedule') }}</h4>
                          <div class="space-y-2">
                              <div>
                                  <div class="text-[#9E9E9E] text-[13px]">{{ $pickup['title'] }}:</div>
                                  <div class="font-semibold text-[14px] text-[#272828]">{{ $pickup['time'] }}</div>
                              </div>
                              <div>
                                  <div class="text-[#9E9E9E] text-[13px]">{{ $delivery['title'] }}:</div>
                                  <div class="font-semibold text-[14px] text-[#272828]">{{ $delivery['time'] }}</div>
                              </div>
                          </div>
                      </div>
                  </div>

                 <!-- соцсети -->
                 <div class="mt-4">
                    <span class="text-[#929292] text-[13px]">{{ st('all.my-v-sotsialnykh-merezhakh','Ми в соціальних мережах') }}</span>
                <ul class="flex items-center mt-2 gap-8 md:gap-8 md:justify-left">
                    <li><a href="https://www.facebook.com/3piroga.ua" target="_blank" aria-label="Facebook" class="text-black hover:text-[#FF7500]">
                            <x-icons.facebook class="w-8 h-8"/>
                        </a></li>
                    <li><a href="https://www.instagram.com/3piroga_ua" target="_blank" aria-label="Instagram"  class="text-black hover:text-[#FF7500]">
                            <x-icons.instagram class="w-8 h-8"/>
                        </a></li>
                    <li><a href="https://www.tiktok.com/@tripiroga?_r=1" aria-label="TikTok" target="_blank" class="text-black hover:text-[#FF7500]">
                            <x-icons.tiktok class="w-8 h-8"/>
                        </a></li>
                    <li><a href="https://t.me/OsetianBakery" target="_blank" aria-label="Telegram" class="text-black hover:text-[#FF7500]">
                            <x-icons.telegram class="w-8 h-8"/>
                        </a></li>
                    <li><a href="viber://chat?number=%2B380660784333" aria-label="Viber" class="text-black hover:text-[#FF7500]">
                            <x-icons.viber class="w-8 h-8"/>
                        </a></li>
                    <li><a href="https://www.youtube.com/channel/UC37VV_ZFmkTacWeHKFsYWLQ" target="_blank" aria-label="YouTube" class="text-black hover:text-[#FF7500]">
                            <x-icons.youtube class="w-8 h-8"/>
                        </a></li>
                </ul>
            </div>
            </div>
        </div>
        </div>
    </aside>
</div>
