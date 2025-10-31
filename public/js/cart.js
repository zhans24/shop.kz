// public/js/cart.js
document.addEventListener("DOMContentLoaded", () => {
    const CART_KEY = "cartItems";

    const getCart  = () => { try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch { return []; } };
    const saveCart = (cart) => localStorage.setItem(CART_KEY, JSON.stringify(cart));
    const parsePrice = (t) => Number((t ?? "").toString().replace(/[^\d]/g, "")) || 0;

    const updateCartCount = () => {
        const cart  = getCart();
        const count = cart.reduce((acc, item) => acc + (item.qty || 0), 0);
        const basketLink = document.querySelector(".basket-link");
        if (!basketLink) return;
        let badge = basketLink.querySelector(".cart-count");
        if (!badge) {
            badge = document.createElement("span");
            badge.classList.add("cart-count");
            badge.style.cssText = "position:absolute;top:-5px;right:-10px;background:red;color:white;font-size:11px;padding:2px 6px;border-radius:10px;";
            basketLink.style.position = "relative";
            basketLink.appendChild(badge);
        }
        badge.textContent = count;
    };

    function showToast(msg, type='info') {
        let t = document.getElementById('toastInline');
        if (!t) {
            t = document.createElement('div');
            t.id = 'toastInline';
            t.style.cssText = 'position:fixed;right:20px;top:20px;z-index:9999;padding:14px 16px;border-radius:12px;color:#fff;box-shadow:0 10px 30px rgba(0,0,0,.15);font-size:14px;opacity:0;';
            document.body.appendChild(t);
        }
        t.style.background = type==='error' ? '#EF4444' : (type==='success' ? '#12B76A' : '#334155');
        t.textContent = (msg ?? '').toString();
        requestAnimationFrame(()=>{ t.style.transition='opacity .2s'; t.style.opacity='1'; });
        setTimeout(()=>{ t.style.opacity='0'; }, 4000);
    }
    window.__cartShowToast = showToast;

    // ===== OOS (нет в наличии) =====
    function readAvailFrom(el) {
        if (!el) return undefined;
        const ds = el.dataset || {};
        const val = ds.available ?? ds.instock ?? ds.stock ?? ds.qty;
        if (typeof val === 'undefined') return undefined;
        const v = String(val).trim().toLowerCase();
        if (v === '' || v === '0' || v === 'false' || v === 'no' || v === 'out') return false;
        return true;
    }
    function isOOSByClassesOrDisabled(btn) {
        if (!btn) return false;
        if (btn.disabled) return true;
        const c = btn.classList;
        return c.contains('out-of-stock') || c.contains('disabled') || c.contains('is-disabled');
    }
    function isOutOfStockButton(btn) {
        if (!btn) return false;
        if (isOOSByClassesOrDisabled(btn)) return true;
        const bAvail = readAvailFrom(btn);
        if (bAvail === false) return true;
        if (bAvail === true)  return false;
        const holder = btn.closest(".prod-item") || btn.closest(".product-item__inner");
        const hAvail = readAvailFrom(holder);
        if (hAvail === false) return true;
        if (hAvail === true)  return false;
        const txt = (btn.textContent || '').trim().toLowerCase();
        if (txt.includes('нет в наличии')) return true;
        return false;
    }
    function markButtonOOS(btn) {
        if (!btn) return;
        btn.disabled = true;
        btn.classList.add('out-of-stock');
        btn.textContent = 'Нет в наличии';
    }

    function hydrateButtonsFromCart() {
        const cart = getCart();
        const codes = new Set((cart || []).map(i => String(i.code || '')));
        document.querySelectorAll(".prod-item__btn, .product-item__btn").forEach((btn) => {
            if (isOutOfStockButton(btn)) { markButtonOOS(btn); return; }
            const code =
                btn.closest(".prod-item")?.dataset.code ||
                btn.closest(".product-item__inner")?.dataset.code || '';
            if (codes.has(String(code || ''))) {
                btn.classList.add("added");
                btn.textContent = "В корзине ✓";
            } else {
                btn.classList.remove("added");
                btn.textContent = "Добавить в корзину";
            }
        });
    }
    window.hydrateButtonsFromCart = hydrateButtonsFromCart;

    function primeOOSButtons() {
        document.querySelectorAll(".prod-item__btn, .product-item__btn").forEach((btn) => {
            if (isOutOfStockButton(btn)) markButtonOOS(btn);
        });
    }

    const renderCart = () => {
        const cartList = document.querySelector(".cart__items");
        if (!cartList) return;

        const cart = getCart();
        cartList.innerHTML = "";

        if (cart.length === 0) {
            cartList.innerHTML = `<p style="padding: 20px;">Корзина пуста</p>`;
            return;
        }

        cart.forEach((item, index) => {
            const oldVal = parsePrice(item.oldPrice);
            const newVal = parsePrice(item.newPrice);
            const hasOld = oldVal > 0 && oldVal > newVal;

            const priceHtml = hasOld
                ? `<p class="old-p" style="text-decoration:line-through;opacity:.6;margin:0;">${item.oldPrice}</p>
           <p class="new-p" style="margin:2px 0 0 0;">${item.newPrice}</p>`
                : `<p class="new-p" style="margin:0;">${item.newPrice || item.oldPrice}</p>`;

            const li = document.createElement("li");
            li.className = "cart__item";
            li.innerHTML = `
        <div class="cart__img"><img src="${item.image || ''}" alt=""></div>
        <div class="cart__item-info">
          <h3 class="cart__item-name">${item.name}</h3>
          <p class="cart__item-code">Код товара: ${item.code}</p>
          <p class="cart__item-date">${item.date}</p>
        </div>
        <div class="cart__item-price">${priceHtml}</div>
        <div class="cart__item-end">
          <div class="cart__item-btn">
            <button class="cart__qty-btn minus">−</button>
            <div class="result-btn">${item.qty}</div>
            <button class="cart__qty-btn plus">+</button>
          </div>
          <button class="cart__item-del">Удалить</button>
        </div>
      `;

            li.querySelector(".plus").addEventListener("click", () => {
                const cart = getCart();
                cart[index].qty++;
                saveCart(cart);
                renderCart(); updateCartCount(); hydrateButtonsFromCart();
                window.dispatchEvent(new Event("cart:changed"));
            });

            li.querySelector(".minus").addEventListener("click", () => {
                const cart = getCart();
                if (cart[index].qty > 1) cart[index].qty--;
                else cart.splice(index, 1);
                saveCart(cart);
                renderCart(); updateCartCount(); hydrateButtonsFromCart();
                window.dispatchEvent(new Event("cart:changed"));
            });

            li.querySelector(".cart__item-del").addEventListener("click", () => {
                const cart = getCart();
                cart.splice(index, 1);
                saveCart(cart);
                renderCart(); updateCartCount(); hydrateButtonsFromCart();
                window.dispatchEvent(new Event("cart:changed"));
            });

            cartList.appendChild(li);
        });
    };

    const addToCart = (product) => {
        const cart = getCart();
        const existing = cart.find((p) => p.code === product.code);
        if (existing) existing.qty++;
        else cart.push(product);
        saveCart(cart);
        updateCartCount(); hydrateButtonsFromCart();
        window.dispatchEvent(new Event("cart:changed"));
    };

    // Кнопки "Добавить в корзину"
    document.querySelectorAll(".prod-item__btn, .product-item__btn").forEach((btn) => {
        btn.addEventListener("click", () => {
            if (btn.classList.contains('added') || btn.dataset.added === '1') {
                (window.__cartShowToast || (()=>{}))('Товар уже в корзине','info');
                return;
            }
            if (isOutOfStockButton(btn)) {
                markButtonOOS(btn);
                (window.__cartShowToast || alert)('Товара нет в наличии', 'error');
                return;
            }

            const prodCard   = btn.closest(".prod-item");
            const singlePage = btn.closest(".product-item__inner");
            let product = null;

            if (prodCard) {
                const code = (prodCard.dataset.code || "").trim();
                if (!code) { alert("Нельзя добавить товар: отсутствует код товара."); return; }
                const newText = (prodCard.querySelector(".prod-item__text span")?.textContent
                    || prodCard.querySelector(".prod-item__text")?.textContent || "").trim();
                const oldText = prodCard.querySelector(".old-p")?.textContent?.trim() || "";

                product = {
                    name: prodCard.querySelector(".prod-item__title")?.textContent.trim() || "Без названия",
                    code,
                    date: new Date().toLocaleDateString("ru-RU"),
                    oldPrice: oldText,
                    newPrice: newText,
                    image: prodCard.querySelector("img")?.src || "",
                    qty: 1,
                };
            } else if (singlePage) {
                const code = (singlePage.dataset.code || "").trim();
                if (!code) { alert("Нельзя добавить товар: отсутствует код товара."); return; }
                product = {
                    name: singlePage.querySelector(".product-item__title")?.textContent.trim() || "Без названия",
                    code,
                    date: new Date().toLocaleDateString("ru-RU"),
                    oldPrice: singlePage.querySelector(".product-item__oldprice")?.textContent?.trim() || "",
                    newPrice: singlePage.querySelector(".product-item__newprice")?.textContent?.trim()
                        || singlePage.querySelector(".product-item__price")?.textContent?.trim() || "",
                    image: singlePage.querySelector(".product-item__hero img")?.src || "",
                    qty: 1,
                };
            } else {
                return;
            }

            btn.classList.add("added");
            btn.dataset.added = '1';
            btn.textContent = "В корзине ✓";
            addToCart(product);
        });
    });

    // init
    primeOOSButtons();
    renderCart(); updateCartCount(); hydrateButtonsFromCart();
    window.dispatchEvent(new Event("cart:changed"));
    window.addEventListener("cart:changed", hydrateButtonsFromCart);
});
