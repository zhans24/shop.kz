document.addEventListener('DOMContentLoaded', () => {
    const locationBlock = document.getElementById('location');
    const dropdown = document.getElementById('locationDropdown');
    const citySpan = document.querySelector('.location__city');

    if (locationBlock && dropdown && citySpan) {
        // 1) Убедимся, что текущий город есть в списке
        const currentLabel = citySpan.textContent.trim();
        const items = Array.from(dropdown.querySelectorAll('li'));

        const hasCurrent =
            items.some(li => (li.dataset.city || li.textContent.trim()) === currentLabel);

        if (!hasCurrent) {
            const li = document.createElement('li');
            li.dataset.city = currentLabel;
            li.textContent = currentLabel;
            dropdown.prepend(li);
        }

        locationBlock.addEventListener('click', (e) => {
            if (e.target && e.target.matches('#locationDropdown li')) return;
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });

        const bindClickHandlers = () => {
            dropdown.querySelectorAll('li').forEach((item) => {
                item.onclick = () => {
                    const slug  = item.dataset.city || item.textContent.trim();
                    const label = item.textContent.trim();

                    citySpan.textContent = label;
                    dropdown.classList.remove('active');

                    localStorage.setItem('citySlug', slug);
                };
            });
        };
        bindClickHandlers();

        document.addEventListener('click', (e) => {
            if (!locationBlock.contains(e.target)) dropdown.classList.remove('active');
        });

        const savedSlug = localStorage.getItem('citySlug');
        if (savedSlug) {
            const li = dropdown.querySelector(`li[data-city="${CSS.escape(savedSlug)}"]`)
                || Array.from(dropdown.querySelectorAll('li'))
                    .find(li => li.textContent.trim() === savedSlug);

            if (li) citySpan.textContent = li.textContent.trim();
        }
    }


    const burger = document.querySelector('.burger')
    const navMenu = document.querySelector('.header__nav')
    const navLinks = document.querySelectorAll('.header__nav ul li a')

    burger?.addEventListener('click', () => {
        navMenu.classList.toggle('active')
        burger.classList.toggle('active')
        if (burger.classList.contains('active')) {
            document.documentElement.style.overflow = 'hidden'
        } else {
            document.documentElement.style.overflow = 'auto'
        }
    })

    const heroSlider = new Swiper('.hero-slider', {
        loop: true,
        speed: 1700,
        spaceBetween: 20,
        slidesPerView: 1,
        navigation: {
            nextEl: '.hero-swiper-button-next',
            prevEl: '.hero-swiper-button-prev',
        },
        autoplay: {
            delay: 5500,
            stopOnLastSlide: false,
            disableOnInteraction: false,
        },
    })

    // Инициализируем все слайдеры "хитов/акций" с локальной навигацией
    document.querySelectorAll('.hits').forEach((section) => {
        const sliderEl = section.querySelector('.hits__slider')
        if (!sliderEl) return

        const nextEl = section.querySelector('.hits__navigation .swiper-button-next')
        const prevEl = section.querySelector('.hits__navigation .swiper-button-prev')

        new Swiper(sliderEl, {
            loop: true,
            speed: 1700,
            spaceBetween: 20,
            slidesPerView: 1,
            initialSlide: 2,
            navigation: { nextEl, prevEl },
            breakpoints: {
                320:  { slidesPerView: 1.4 },
                540:  { slidesPerView: 2.2 },
                767:  { slidesPerView: 2.7 },
                998:  { slidesPerView: 3.5 },
                1140: { slidesPerView: 4.7 },
            },
            autoplay: {
                delay: 5500,
                stopOnLastSlide: false,
                disableOnInteraction: false,
            },
        })
    })

    window.addEventListener("scroll", function () {
        const header = document.querySelector(".header__sticky")
        if (!header) return;
        if (window.scrollY > 50) {
            header.style.top = "20px"
        } else {
            header.style.top = "80px"
        }
    })

    // ===== СОРТ / кастомный селект: теперь реально сабмитит форму =====
    const select = document.querySelector(".custom-select")
    const sortForm   = document.getElementById('sortForm')
    const sortHidden = sortForm ? sortForm.querySelector('input[name="sort"]') : null

    if (select && sortForm && sortHidden) {
        const selected = select.querySelector(".select-selected")
        const selectedSpan = select.querySelector(".selected-span")
        const options = select.querySelectorAll(".select-options li")

        selected.addEventListener("click", () => {
            select.classList.toggle("active")
        })

        options.forEach((option) => {
            option.addEventListener("click", () => {
                selectedSpan.textContent = option.textContent
                select.classList.remove("active")
                // главное: записать значение и отправить GET
                sortHidden.value = option.dataset.value || ''
                sortForm.requestSubmit()
            })
        })

        document.addEventListener("click", (e) => {
            if (!select.contains(e.target)) {
                select.classList.remove("active")
            }
        })
    }

    // ===== СЛАЙДЕР ЦЕН: ручной ввод больше НЕ блокируется gap-ом =====
    const rangeMin = document.getElementById("rangeMin")
    const rangeMax = document.getElementById("rangeMax")
    const minPrice = document.getElementById("minPrice")
    const maxPrice = document.getElementById("maxPrice")
    const progress = document.querySelector(".progress")

    if (rangeMin && rangeMax && minPrice && maxPrice && progress) {
        const MAX_LIMIT = parseInt(rangeMax.max) || 500000
        const STEP      = parseInt(rangeMax.step) || 1000
        const PRICE_GAP = 10000 // применяем только к ползункам

            // страхуем number-поля
        ;[minPrice, maxPrice].forEach(inp => {
            inp.setAttribute('min', '0')
            inp.setAttribute('max', String(MAX_LIMIT))
            inp.setAttribute('step', String(STEP))
            inp.setAttribute('inputmode', 'numeric')
        })

        const clamp = (v) => {
            const n = parseInt(v)
            if (Number.isNaN(n)) return 0
            return Math.min(Math.max(n, 0), MAX_LIMIT)
        }

        const draw = (min, max) => {
            progress.style.left  = (min / MAX_LIMIT) * 100 + "%"
            progress.style.right = 100 - (max / MAX_LIMIT) * 100 + "%"
        }

        // Ручной ввод: разрешаем любой min<=max, без принудительного PRICE_GAP
        const syncFromInputs = () => {
            let min = clamp(minPrice.value)
            let max = clamp(maxPrice.value)
            if (max < min) max = min

            rangeMin.value = String(min)
            rangeMax.value = String(max)
            draw(min, max)
        }

        // Ползунки: держим разрыв PRICE_GAP
        const syncFromRanges = (e) => {
            let min = clamp(rangeMin.value)
            let max = clamp(rangeMax.value)

            if (max - min < PRICE_GAP) {
                if (e && e.target && e.target.id === 'rangeMin') {
                    min = Math.min(max - PRICE_GAP, MAX_LIMIT - PRICE_GAP)
                    rangeMin.value = String(min)
                } else {
                    max = Math.max(min + PRICE_GAP, PRICE_GAP)
                    rangeMax.value = String(max)
                }
            }

            minPrice.value = String(min)
            maxPrice.value = String(max)
            draw(min, max)
        }

        rangeMin.addEventListener("input", syncFromRanges)
        rangeMax.addEventListener("input", syncFromRanges)

        // Вручную меняем — обновляем мягко (на blur/change, чтобы можно было печатать)
        ;['change','blur'].forEach(evt => {
            minPrice.addEventListener(evt, syncFromInputs)
            maxPrice.addEventListener(evt, syncFromInputs)
        })

        // первичный рендер
        syncFromInputs()
    }

    // ===== Галерея товара / табы (как было) =====
    const thumbsContainer = document.querySelector('.thumbs-swiper')

    if (thumbsContainer) {
        const thumbsSwiper = new Swiper(thumbsContainer, {
            spaceBetween: 30,
            slidesPerView: 3,
            freeMode: true,
            watchSlidesProgress: true,
        })

        const productSwiper = new Swiper('.product-swiper', {
            spaceBetween: 10,
            thumbs: { swiper: thumbsSwiper },
            preventClicks: false,
            preventClicksPropagation: false,
        })
        const tabButtons = document.querySelectorAll('.tab-btn')
        const tabContents = document.querySelectorAll('.tab-content')

        tabButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                tabButtons.forEach((b) => b.classList.remove('active'))
                tabContents.forEach((c) => c.classList.remove('active'))
                btn.classList.add('active')
                document.getElementById(btn.dataset.tab).classList.add('active')
            })
        })
    }

    const similarSwiper = document.querySelector('.similar-swiper')
    if (similarSwiper) {
        new Swiper(similarSwiper, {
            slidesPerView: 2.4,
            spaceBetween: 10,
            loop: true,
            navigation: { nextEl: '.similar-btn-next', prevEl: '.similar-btn-prev' },
            breakpoints: {
                767: { slidesPerView: 3.2, spaceBetween: 20 },
                1200:{ slidesPerView: 4.4, spaceBetween: 30 }
            },
        })
    }

    const recommendSwiper = document.querySelector('.recommend-swiper')
    if (recommendSwiper) {
        new Swiper(recommendSwiper, {
            slidesPerView: 2.4,
            spaceBetween: 30,
            loop: true,
            navigation: { nextEl: '.recommend-btn-next', prevEl: '.recommend-btn-prev' },
            breakpoints: {
                767: { slidesPerView: 3.4 },
                992: { slidesPerView: 4.4 },
            },
        })
    }

    const reviewSlide = document.querySelector('.reviews-swiper')
    if (reviewSlide) {
        new Swiper(reviewSlide, {
            slidesPerView: 4.1,
            spaceBetween: 15,
            loop: true,
            navigation: { nextEl: '.reviews-btn-next', prevEl: '.reviews-btn-prev' },
            breakpoints: {
                320:  { slidesPerView: 2.1, spaceBetween: 10 },
                768:  { slidesPerView: 2.4, spaceBetween: 20 },
                992:  { slidesPerView: 3.1, spaceBetween: 25 },
                1280: { slidesPerView: 4.1, spaceBetween: 30 },
            },
        })
    }

    // ===== Предпросмотр файлов отзыва =====
    // Ожидаем input[type=file] с id="reviewPhotos" и контейнер #uploadPreview
    const fileInput = document.getElementById('reviewPhotos')
    const previewBox = document.getElementById('uploadPreview')

    if (fileInput && previewBox) {
        const renderPreview = (files) => {
            previewBox.innerHTML = ''
            if (!files || !files.length) return

            Array.from(files).slice(0, 5).forEach((file, idx) => {
                const row = document.createElement('div')
                row.className = 'upload-preview__item'
                row.style.cssText = 'display:flex;align-items:center;gap:10px;margin:6px 0;'

                const thumb = document.createElement('img')
                thumb.alt = file.name
                thumb.style.cssText = 'width:56px;height:56px;object-fit:cover;border-radius:8px;border:1px solid #eee;'

                const meta = document.createElement('div')
                meta.style.cssText = 'display:flex;flex-direction:column;gap:2px;'
                meta.innerHTML = `<strong style="font-size:13px">${file.name}</strong>
                                  <span style="font-size:12px;opacity:.6">${(file.size/1024/1024).toFixed(2)} МБ</span>`

                // превью только для изображений
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader()
                    reader.onload = e => { thumb.src = e.target.result }
                    reader.readAsDataURL(file)
                } else {
                    thumb.src = ''
                    thumb.style.display = 'none'
                }

                row.appendChild(thumb)
                row.appendChild(meta)
                previewBox.appendChild(row)
            })
        }

        fileInput.addEventListener('change', (e) => {
            renderPreview(e.target.files)
        })
    }
})

$(function () {
    var $phone = $('#phone');
    if ($phone.length && typeof $phone.mask === 'function') {
        $phone.mask('+7 999 999 99 99');
    }
});

const filterForm = document.getElementById('filterForm');
const resetBtn = document.getElementById('filterReset');
if (filterForm && resetBtn) {
    resetBtn.addEventListener('click', () => {
        const brand = filterForm.querySelector('[name="brand_id"]');
        const type  = filterForm.querySelector('[name="type"]');
        const minI  = filterForm.querySelector('#minPrice');
        const maxI  = filterForm.querySelector('#maxPrice');
        const rMin  = filterForm.querySelector('#rangeMin');
        const rMax  = filterForm.querySelector('#rangeMax');
        const progress = filterForm.querySelector('.progress');

        if (brand) brand.value = '';
        if (type)  type.value  = '';

        const maxLimit = rMax ? parseInt(rMax.max) : 500000;
        if (minI) minI.value = '0';
        if (maxI) maxI.value = String(maxLimit);
        if (rMin) rMin.value = '0';
        if (rMax) rMax.value = String(maxLimit);
        if (progress) {
            progress.style.left = '0%';
            progress.style.right = '0%';
        }

       filterForm.requestSubmit();
    });



}
