document.addEventListener("DOMContentLoaded", () => {
  const CART_KEY = "cartItems"

  // ==== Вспомогательные функции ====
  const getCart = () => JSON.parse(localStorage.getItem(CART_KEY)) || []
  const saveCart = (cart) => localStorage.setItem(CART_KEY, JSON.stringify(cart))

  const updateCartCount = () => {
    const cart = getCart()
    const count = cart.reduce((acc, item) => acc + item.qty, 0)
    const basketLink = document.querySelector(".basket-link")

    if (!basketLink) return // если иконки корзины нет — пропускаем

    let badge = basketLink.querySelector(".cart-count")
    if (!badge) {
      badge = document.createElement("span")
      badge.classList.add("cart-count")
      badge.style.cssText = `
        position:absolute;
        top:-5px;
        right:-10px;
        background:red;
        color:white;
        font-size:11px;
        padding:2px 6px;
        border-radius:10px;
      `
      basketLink.style.position = "relative"
      basketLink.appendChild(badge)
    }
    badge.textContent = count
  }

  const renderCart = () => {
    const cartList = document.querySelector(".cart__items")
    if (!cartList) return // если корзины на странице нет — не рендерим

    const cart = getCart()
    cartList.innerHTML = ""

    if (cart.length === 0) {
      cartList.innerHTML = `<p style="padding: 20px;">Корзина пуста 😔</p>`
      return
    }

    cart.forEach((item, index) => {
      const li = document.createElement("li")
      li.className = "cart__item"
      li.innerHTML = `
        <div class="cart__img"><img src="${item.image}" alt=""></div>
        <div class="cart__item-info">
          <h3 class="cart__item-name">${item.name}</h3>
          <p class="cart__item-code">Код товара: ${item.code}</p>
          <p class="cart__item-date">${item.date}</p>
        </div>
        <div class="cart__item-price">
          ${item.oldPrice ? `<p class="old-p">${item.oldPrice}</p>` : ""}
          <p class="new-p">${item.newPrice}</p>
        </div>
        <div class="cart__item-end">
          <div class="cart__item-btn">
            <button class="cart__qty-btn minus">−</button>
            <div class="result-btn">${item.qty}</div>
            <button class="cart__qty-btn plus">+</button>
          </div>
          <button class="cart__item-del">Удалить</button>
        </div>
      `

      // кнопки + -
      li.querySelector(".plus").addEventListener("click", () => {
        cart[index].qty++
        saveCart(cart)
        renderCart()
        updateCartCount()
      })

      li.querySelector(".minus").addEventListener("click", () => {
        if (cart[index].qty > 1) {
          cart[index].qty--
        } else {
          cart.splice(index, 1)
        }
        saveCart(cart)
        renderCart()
        updateCartCount()
      })

      // удалить товар
      li.querySelector(".cart__item-del").addEventListener("click", () => {
        cart.splice(index, 1)
        saveCart(cart)
        renderCart()
        updateCartCount()
      })

      cartList.appendChild(li)
    })
  }

  // ==== Добавить товар в корзину ====
  const addToCart = (product) => {
    const cart = getCart()
    const existing = cart.find((p) => p.code === product.code)
    if (existing) {
      existing.qty++
    } else {
      cart.push(product)
    }
    saveCart(cart)
    updateCartCount()
  }

  // ==== Универсальная обработка кнопок ====
  const buttons = document.querySelectorAll(".prod-item__btn, .product-item__btn")

  buttons.forEach((btn) => {
    btn.addEventListener("click", () => {
      // карточка каталога
      const prodCard = btn.closest(".prod-item")
      // карточка страницы товара
      const singlePage = btn.closest(".product-item__inner")

      let product = {}

      if (prodCard) {
        product = {
          name: prodCard.querySelector(".prod-item__title")?.textContent.trim() || "Без названия",
          code: prodCard.dataset.code || Math.random().toString(36).substring(2, 8),
          date: new Date().toLocaleDateString("ru-RU"),
          oldPrice: prodCard.querySelector(".old-p")?.textContent || "",
          newPrice: prodCard.querySelector(".prod-item__text")?.textContent || "",
          image: prodCard.querySelector("img")?.src || "",
          qty: 1,
        }
      } else if (singlePage) {
        product = {
          name: singlePage.querySelector(".product-item__title")?.textContent.trim() || "Без названия",
          code: singlePage.dataset.code || Math.random().toString(36).substring(2, 8),
          date: new Date().toLocaleDateString("ru-RU"),
          oldPrice: singlePage.querySelector(".product-item__oldprice")?.textContent || "",
          newPrice: singlePage.querySelector(".product-item__price")?.textContent || "",
          image: singlePage.querySelector(".product-item__hero img")?.src || "",
          qty: 1,
        }
      } else {
        return
      }

      btn.classList.add("added")
      btn.textContent = "В корзине ✓"

      addToCart(product)
    })
  })

  // ==== Запуск ====
  renderCart()
  updateCartCount()
})
