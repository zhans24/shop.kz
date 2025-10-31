<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','TechnoStyle')</title>
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @endif

    @php
        function vasset($path) {
            $full = public_path($path);
            return asset($path) . (file_exists($full) ? ('?v='.filemtime($full)) : '');
        }
        $routeName   = optional(request()->route())->getName();
        $isLightPage = in_array($routeName, ['front.home','front.about'], true);
        $headerTheme = $headerTheme ?? ($isLightPage ? 'light' : 'dark');
        $headerMod   = $headerMod   ?? ($headerTheme === 'dark' ? 'pages-header__sticky' : '');
    @endphp

    <link rel="icon" type="image/png" href="{{ asset('img/logo.svg') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="{{ vasset('css/main.css') }}">
    <link rel="stylesheet" href="{{ vasset('css/styles.css') }}">

    <style>
        /* ——— Лёгкий лайтбокс (модалка для изображений) ——— */
        .lb{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;z-index:1000}
        .lb[hidden]{display:none}
        .lb__backdrop{position:absolute;inset:0;background:rgba(0,0,0,.85);backdrop-filter:saturate(120%) blur(2px)}
        .lb__content{position:relative;margin:0;max-width:90vw;max-height:90vh;animation:lbIn .12s ease-out}
        .lb__content img{max-width:90vw;max-height:90vh;display:block;border-radius:10px;box-shadow:0 10px 40px rgba(0,0,0,.5)}
        .lb__caption{position:absolute;left:0;right:0;bottom:-32px;color:#fff;opacity:.85;font-size:14px;text-align:center}
        .lb__close{position:absolute;top:-12px;right:-12px;width:36px;height:36px;border:0;border-radius:50%;background:#fff;box-shadow:0 6px 20px rgba(0,0,0,.25);cursor:pointer;font-size:18px;line-height:36px}
        @keyframes lbIn{from{transform:scale(.98);opacity:.75}to{transform:scale(1);opacity:1}}
        .product-item__gallery .swiper-slide a[data-viewer]{pointer-events:auto;cursor:zoom-in}
    </style>
</head>

<body>
<div class="wrapper">
    @include('partials.header', [
        'headerTheme' => $headerTheme,
        'headerModifier' => $headerMod,
    ])

    <main>@yield('content')</main>

    @include('partials.footer')
    @include('partials.toast_success')
</div>

{{-- порядок: jQuery → плагины → Swiper → ваши скрипты --}}
<script src="{{ vasset('js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ vasset('js/jquery.maskedinput.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
<script src="{{ vasset('js/index.js') }}"></script>

<!-- Лайтбокс (один раз на весь сайт) -->
<div id="imgLightbox" class="lb" hidden>
    <div class="lb__backdrop" data-close></div>
    <figure class="lb__content" role="dialog" aria-modal="true" aria-label="Просмотр изображения">
        <img id="lbImg" alt="" decoding="async">
        <figcaption id="lbCap" class="lb__caption"></figcaption>
        <button class="lb__close" type="button" aria-label="Закрыть" data-close>✕</button>
    </figure>
</div>

@stack('scripts')

<script>
    (function () {
        const input = document.getElementById('search');
        if (!input) return;

        const SUGGEST_URL = '/ajax/suggest';
        const SEARCH_URL  = '/search';
        const BOX_ID = 'searchSuggestBox';

        let box = document.getElementById(BOX_ID);
        if (!box) {
            box = document.createElement('div');
            box.id = BOX_ID;
            box.className = 'search-suggest';
            document.body.appendChild(box);
        }

        let items = [];
        let idx   = -1;
        let tmr   = null;
        let lastQuery = '';

        function position() {
            const r = input.getBoundingClientRect();
            const pad = 6, EXTRA = 120, MIN_W = 320, MAX_W = 560, SAFE = 12;

            let w = Math.min(
                Math.max(r.width + EXTRA, MIN_W),
                MAX_W,
                window.innerWidth - SAFE * 2
            );

            let left = window.scrollX + r.left + (r.width - w) / 2;
            left = Math.max(SAFE, Math.min(left, window.scrollX + window.innerWidth - w - SAFE));

            box.style.width = w + 'px';
            box.style.left  = left + 'px';
            box.style.top   = (window.scrollY + r.bottom + pad) + 'px';
        }

        function hide() { box.style.display = 'none'; idx = -1; }
        function show() {
            position();
            if (box.innerHTML.trim()) box.style.display = 'block';
        }

        function select(i) {
            idx = i;
            box.querySelectorAll('.suggest-row').forEach((row, k) => {
                row.classList.toggle('is-active', k === idx);
            });
        }

        function render() {
            box.innerHTML = '';
            if (!items.length) {
                box.innerHTML = `
              <div class="empty">Ничего не найдено</div>
              <div class="suggest-footer">
                <a class="suggest-more" href="${SEARCH_URL}?q=${encodeURIComponent(lastQuery)}">Показать всё</a>
              </div>`;
                show(); return;
            }
            const max = Math.min(items.length, 5);
            for (let i = 0; i < max; i++) {
                const it = items[i];
                const a  = document.createElement('a');
                a.href   = it.url;
                a.className = 'suggest-row';
                a.innerHTML = `
              <img class="thumb" src="${it.img}" alt="">
              <div class="meta">
                <span class="name">${it.name}</span>
                <span class="sku">${it.sku ?? ''}</span>
              </div>
              <span class="price">${it.price}</span>`;
                a.addEventListener('mouseenter', () => select(i));
                box.appendChild(a);
            }
            const footer = document.createElement('div');
            footer.className = 'suggest-footer';
            footer.innerHTML = `<a class="suggest-more" href="${SEARCH_URL}?q=${encodeURIComponent(lastQuery)}">Показать всё</a>`;
            box.appendChild(footer);
            select(-1); show();
        }

        function fetchSuggest(q) {
            lastQuery = q;
            fetch(SUGGEST_URL + '?s=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
                .then(r => r.ok ? r.json() : [])
                .then(data => { items = Array.isArray(data) ? data : []; render(); })
                .catch(() => hide());
        }

        input.addEventListener('input', () => {
            const v = (input.value || '').trim();
            if (v.length < 2) { hide(); return; }
            clearTimeout(tmr);
            tmr = setTimeout(() => fetchSuggest(v), 220);
        });
        input.addEventListener('keydown', (e) => {
            if (box.style.display !== 'block') return;
            if (e.key === 'ArrowDown') { e.preventDefault(); select(Math.min(idx + 1, Math.max(items.length - 1, 0))); }
            if (e.key === 'ArrowUp')   { e.preventDefault(); select(Math.max(idx - 1, 0)); }
            if (e.key === 'Enter') {
                if (idx >= 0 && items[idx]) window.location.href = items[idx].url;
                else window.location.href = SEARCH_URL + '?q=' + encodeURIComponent(input.value || '');
                e.preventDefault(); hide();
            }
            if (e.key === 'Escape') hide();
        });
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && (idx < 0 || box.style.display !== 'block')) {
                window.location.href = SEARCH_URL + '?q=' + encodeURIComponent(input.value || '');
            }
        });
        input.addEventListener('focus', () => {
            if ((input.value || '').trim().length >= 2 && (items.length || box.querySelector('.empty'))) show();
        });
        input.addEventListener('blur', () => setTimeout(hide, 150));
        document.addEventListener('click', (e) => { if (e.target === input || box.contains(e.target)) return; hide(); });
        window.addEventListener('resize', position, { passive: true });
        window.addEventListener('scroll', position, true);
    })();
</script>
<script>window.__AUTH__ = @json(auth()->check());</script>
@auth
    @if (auth()->user()->hasVerifiedEmail())
        <script src="{{ vasset('js/cart-auth.js') }}"></script>
    @else
        <script src="{{ vasset('js/cart.js') }}"></script>
    @endif
@else
    <script src="{{ vasset('js/cart.js') }}"></script>
@endauth
</body>
</html>
