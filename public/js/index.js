(function () {
    // дождаться, когда выполнится всё с defer
    document.addEventListener('DOMContentLoaded', function () {

        // ---- jQuery-плагины (маска телефона) ----
        try {
            if (window.jQuery && jQuery.fn && jQuery.fn.mask) {
                jQuery('input[type="tel"], .phone-input').mask('+7 999 999 99 99');
            }
        } catch (e) {
            console.warn('Mask init skipped:', e);
        }

        // ---- Swiper hero ----
        try {
            if (window.Swiper) {
                var heroEl = document.querySelector('.hero-slider.swiper');
                if (heroEl) {
                    new Swiper(heroEl, {
                        loop: true,
                        speed: 600,
                        autoplay: { delay: 4000 },
                        pagination: {
                            el: '.hero .swiper-pagination',
                            clickable: true
                        },
                        navigation: {
                            nextEl: '.hero-swiper-button-next',
                            prevEl: '.hero-swiper-button-prev'
                        }
                    });
                }

                // ---- Swiper "Хиты" / "Акции" ----
                document.querySelectorAll('.hits__slider.swiper').forEach(function (el) {
                    new Swiper(el, {
                        slidesPerView: 1.2,
                        spaceBetween: 16,
                        breakpoints: {
                            576: { slidesPerView: 2.2, spaceBetween: 20 },
                            768: { slidesPerView: 3,   spaceBetween: 24 },
                            1200:{ slidesPerView: 4,   spaceBetween: 24 }
                        },
                        navigation: {
                            nextEl: el.parentElement.querySelector('.swiper-button-next'),
                            prevEl: el.parentElement.querySelector('.swiper-button-prev')
                        }
                    });
                });
            } else {
                console.warn('Swiper not found – проверь тег CDN.');
            }
        } catch (e) {
            console.error('Swiper init error:', e);
        }

    });
})();


document.addEventListener('DOMContentLoaded', () => {
  const locationBlock = document.getElementById('location')
  const dropdown = document.getElementById('locationDropdown')
  const citySpan = document.querySelector('.location__city')

  locationBlock.addEventListener('click', (e) => {
    e.stopPropagation()
    dropdown.classList.toggle('active')
  })

  dropdown.querySelectorAll('li').forEach(item => {
    item.addEventListener('click', () => {
      citySpan.textContent = item.dataset.city
      dropdown.classList.remove('active')
    })
  })

  document.addEventListener('click', (e) => {
    if (!locationBlock.contains(e.target)) {
      dropdown.classList.remove('active')
    }
  })


  const burger = document.querySelector('.burger')
  const navMenu = document.querySelector('.header__nav')
  const navLinks = document.querySelectorAll('.header__nav ul li a')

  burger.addEventListener('click', () => {
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
    initialSlide: 2,
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

  const hitSlider = new Swiper('.hits__slider', {
    loop: true,
    speed: 1700,
    spaceBetween: 20,
    slidesPerView: 1,
    initialSlide: 2,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      320: {
        slidesPerView: 1.4,
      },
      540: {
        slidesPerView: 2.2,
      },
      767: {
        slidesPerView: 2.7,
      },
      998: {
        slidesPerView: 3.5,
      },
      1140: {
        slidesPerView: 4.7,
      },
    },
    autoplay: {
      delay: 5500,
      stopOnLastSlide: false,
      disableOnInteraction: false,
    },
  })
  window.addEventListener("scroll", function () {
    const header = document.querySelector(".header__sticky")
    const headerHeight = header.clientHeight

    if (window.scrollY > 50) {
      header.style.top = "20px"
    } else {
      header.style.top = "80px"
    }


    // if (window.innerWidth < 993) {
    //   if (window.scrollY > 100) {
    //     header.style.top = "20px";
    //   } else {
    //     header.style.top = "80px";
    //   }
    // }

  })


  const select = document.querySelector(".custom-select")


  if (select) {
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
      })
    })

    document.addEventListener("click", (e) => {
      if (!select.contains(e.target)) {
        select.classList.remove("active")
      }
    })
  }

  const rangeMin = document.getElementById("rangeMin")
  const rangeMax = document.getElementById("rangeMax")
  const minPrice = document.getElementById("minPrice")
  const maxPrice = document.getElementById("maxPrice")
  const progress = document.querySelector(".progress")

  const priceGap = 10000

  if (rangeMin) {
    function updateSlider() {
      const min = parseInt(rangeMin.value)
      const max = parseInt(rangeMax.value)
      const maxLimit = parseInt(rangeMax.max)

      progress.style.left = (min / maxLimit) * 100 + "%"
      progress.style.right = 100 - (max / maxLimit) * 100 + "%"

      minPrice.value = min
      maxPrice.value = max
    }

    function handleRangeInput(e) {
      let min = parseInt(rangeMin.value)
      let max = parseInt(rangeMax.value)

      if (max - min < priceGap) {
        if (e.target.id === "rangeMin") {
          rangeMin.value = max - priceGap
        } else {
          rangeMax.value = min + priceGap
        }
      }
      updateSlider()
    }

    function handleNumberInput() {
      let min = parseInt(minPrice.value)
      let max = parseInt(maxPrice.value)

      if (max - min >= priceGap && max <= 500000) {
        rangeMin.value = min
        rangeMax.value = max
        updateSlider()
      }
    }

    rangeMin.addEventListener("input", handleRangeInput)
    rangeMax.addEventListener("input", handleRangeInput)
    minPrice.addEventListener("input", handleNumberInput)
    maxPrice.addEventListener("input", handleNumberInput)

    updateSlider()

  }

  const thumbsContainer = document.querySelector('.thumbs-swiper')

  if (thumbsContainer) {
    const thumbsSwiper = new Swiper(thumbsContainer, {
      spaceBetween: 30,
      slidesPerView: 3,
      freeMode: true,
      watchSlidesProgress: true,
    })

    // создаём Swiper для главного изображения и связываем с миниатюрами
    const productSwiper = new Swiper('.product-swiper', {
      spaceBetween: 10,
      thumbs: {
        swiper: thumbsSwiper, // ✅ здесь уже Swiper-экземпляр, не DOM
      },
    })

    // Tabs
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
    const swiper = new Swiper(similarSwiper, {
      slidesPerView: 2.4,
      spaceBetween: 10,
      loop: true,
      navigation: {
        nextEl: '.similar-btn-next',
        prevEl: '.similar-btn-prev',
      },
      breakpoints: {
        767: {
          slidesPerView: 3.2,
          spaceBetween: 20,
        },
        1200: {
          slidesPerView: 4.4,
          spaceBetween: 30,
        }
      },
    })
  }
  const recommendSwiper = document.querySelector('.recommend-swiper')

  if (recommendSwiper) {
    const swiper = new Swiper(recommendSwiper, {
      slidesPerView: 2.4,
      spaceBetween: 30,
      loop: true,
      navigation: {
        nextEl: '.recommend-btn-next',
        prevEl: '.recommend-btn-prev',
      },
      breakpoints: {
        767: {
          slidesPerView: 3.4,
        },
        992: {
          slidesPerView: 4.4,
        }
      },
    })
  }


  const reviewSlide = document.querySelector('.reviews-swiper')

  if (reviewSlide) {
    const swiper = new Swiper(reviewSlide, {
      slidesPerView: 4.1,
      spaceBetween: 15,
      loop: true,
      navigation: {
        nextEl: '.reviews-btn-next',
        prevEl: '.reviews-btn-prev',
      },
      breakpoints: {
        320: {
          slidesPerView: 2.1,
          spaceBetween: 10,
        },
        768: {
          slidesPerView: 2.4,
          spaceBetween: 20,
        },
        992: {
          slidesPerView: 3.1,
          spaceBetween: 25,
        },
        1280: {
          slidesPerView: 4.1,
          spaceBetween: 30,
        },
      },
    })
  }


})

