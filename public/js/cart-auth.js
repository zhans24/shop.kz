// public/js/cart-auth.js
(function () {
    if (window.__CART_AUTH_INIT_DONE__) return;
    window.__CART_AUTH_INIT_DONE__ = true;

    document.addEventListener("DOMContentLoaded", () => {
        const CART_KEY     = "cartItems";
        const API_SYNC     = "/cart/sync";
        const CSRF         = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const USER_ID_RAW  = (typeof window.__USER_ID__ === 'number' || typeof window.__USER_ID__ === 'string') ? String(window.__USER_ID__) : 'anon';
        const USER_ID      = USER_ID_RAW || 'anon';
        const SYNC_FLAGKey = `cartSyncDone:${USER_ID}`; // флаг в localStorage

        // ===== Helpers =====
        const getCart = () => { try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch { return []; } };
        const saveCart = (cart) => localStorage.setItem(CART_KEY, JSON.stringify(cart));
        const parsePrice = (t) => Number((t ?? "").toString().replace(/[^\d]/g, "")) || 0;

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

        // ===== OOS (нет в наличии) детектор =====
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
            // 1) data-* на кнопке
            const bAvail = readAvailFrom(btn);
            if (bAvail === false) return true;
            if (bAvail === true)  return false;
            // 2) data-* на карточке товара (.prod-item или .product-item__inner)
            const holder = btn.closest(".prod-item") || btn.closest(".product-item__inner");
            const hAvail = readAvailFrom(holder);
            if (hAvail === false) return true;
            if (hAvail === true)  return false;
            // 3) по текущему тексту
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

        // Гидрация (после перезагрузки/синков) — учитывает OOS
        function hydrateButtonsFromCart() {
            const cart = getCart();
            const codes = new Set((cart || []).map(i => String(i.code || '')));
            document.querySelectorAll(".prod-item__btn, .product-item__btn").forEach((btn) => {
                // если OOS — фиксируем внешний вид и выходим
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

        // Пройтись по всем карточкам и сразу отметить OOS в похожих/рекомендованных
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
                    : `<p class="new-p" style="margin:0;">${item.newPrice || item.oldPrice || ''}</p>`;

                const li = document.createElement("li");
                li.className = "cart__item";
                li.innerHTML = `
          <div class="cart__img"><img src="${item.image || ''}" alt=""></div>
          <div class="cart__item-info">
            <h3 class="cart__item-name">${item.name || 'Товар'}</h3>
            <p class="cart__item-code">Код товара: ${item.code}</p>
            <p class="cart__item-date">${item.date || ''}</p>
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
                    pushOverwriteThrottled();
                    renderCart(); updateCartCount(); hydrateButtonsFromCart();
                    window.dispatchEvent(new Event("cart:changed"));
                });

                li.querySelector(".minus").addEventListener("click", () => {
                    const cart = getCart();
                    if (cart[index].qty > 1) cart[index].qty--;
                    else cart.splice(index, 1);
                    saveCart(cart);
                    pushOverwriteThrottled();
                    renderCart(); updateCartCount(); hydrateButtonsFromCart();
                    window.dispatchEvent(new Event("cart:changed"));
                });

                li.querySelector(".cart__item-del").addEventListener("click", () => {
                    const cart = getCart();
                    cart.splice(index, 1);
                    saveCart(cart);
                    pushOverwriteThrottled();
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
            pushOverwriteThrottled();
            updateCartCount(); hydrateButtonsFromCart();
            window.dispatchEvent(new Event("cart:changed"));
        };

        // Кнопки «Добавить»
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

        // ===== Server sync (overwrite-only) =====
        let __syncTimer = null;
        const throttle = (fn, delay=800) => (...args) => {
            clearTimeout(__syncTimer);
            __syncTimer = setTimeout(() => fn(...args), delay);
        };

        const localToSimple = (arr) =>
            (arr || []).filter(i => String(i.code||'').trim().length).map(i => ({
                code: String(i.code), qty: Math.max(1, +i.qty || 0)
            }));

        function enrichFromServerOrLocal(serverItems, baseLocal) {
            const baseByCode = new Map((baseLocal || []).map(it => [String(it.code||''), it]));
            return (serverItems || []).map(it => {
                const code = String(it.code || '');
                const qty  = Math.max(1, +it.qty || 0);
                const base = baseByCode.get(code) || {};
                return {
                    code,
                    qty,
                    name     : it.name ?? base.name ?? 'Товар',
                    image    : it.image ?? base.image ?? '',
                    newPrice : it.newPrice ?? base.newPrice ?? '',
                    oldPrice : it.oldPrice ?? base.oldPrice ?? '',
                    date     : base.date || new Date().toLocaleDateString("ru-RU"),
                };
            });
        }

        async function fetchServer() {
            const r = await fetch(API_SYNC, { credentials: 'same-origin' });
            if (!r.ok) return { items: [] };
            return r.json();
        }

        async function pushOverwrite(simpleItems) {
            await fetch(API_SYNC, {
                method: 'PUT',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ items: simpleItems, merge: false }),
            });
        }

        const pushOverwriteThrottled = throttle(async () => {
            try { await pushOverwrite(localToSimple(getCart())); } catch {}
        }, 800);

        async function initialSync() {
            // если уже синкали на этом устройстве для этого пользователя — доверяем серверу
            if (localStorage.getItem(SYNC_FLAGKey) === '1') {
                try {
                    const data = await fetchServer();
                    const serverItems = Array.isArray(data.items) ? data.items : [];
                    const trusted = enrichFromServerOrLocal(serverItems, getCart());
                    saveCart(trusted);
                    renderCart(); updateCartCount(); hydrateButtonsFromCart();
                    window.dispatchEvent(new Event("cart:changed"));
                } catch {}
                return;
            }

            try {
                const data = await fetchServer();
                const serverItems = Array.isArray(data.items) ? data.items : [];
                const localRich   = getCart();
                const localSimple = localToSimple(localRich);

                const hasServer = serverItems.length > 0;
                const hasLocal  = localSimple.length > 0;

                if (hasServer && !hasLocal) {
                    const rich = enrichFromServerOrLocal(serverItems, localRich);
                    saveCart(rich);
                    renderCart(); updateCartCount(); hydrateButtonsFromCart();
                    window.dispatchEvent(new Event("cart:changed"));
                    localStorage.setItem(SYNC_FLAGKey, '1');
                    return;
                }

                if (!hasServer && hasLocal) {
                    await pushOverwrite(localSimple);
                    localStorage.setItem(SYNC_FLAGKey, '1');
                    return;
                }

                if (hasServer && hasLocal) {
                    // одноразовый merge: без удвоений — берём максимум qty
                    const mapLocal  = new Map(localSimple.map(i  => [String(i.code), Math.max(1, +i.qty || 0)]));
                    const mapServer = new Map(serverItems.map(i => [String(i.code), Math.max(1, +i.qty || 0)]));

                    const allCodes = new Set([...mapLocal.keys(), ...mapServer.keys()]);
                    const mergedSimple = Array.from(allCodes).map(code => {
                        const l = mapLocal.get(code)  ?? 0;
                        const s = mapServer.get(code) ?? 0;
                        return { code, qty: Math.max(l, s) };
                    });

                    const mergedRich = enrichFromServerOrLocal(mergedSimple, localRich);
                    saveCart(mergedRich);
                    renderCart(); updateCartCount(); hydrateButtonsFromCart();
                    window.dispatchEvent(new Event("cart:changed"));

                    await pushOverwrite(mergedSimple); // overwrite, НЕ merge
                    localStorage.setItem(SYNC_FLAGKey, '1');
                    return;
                }

                // пусто везде
                localStorage.setItem(SYNC_FLAGKey, '1');
            } catch {}
        }

        // init
        primeOOSButtons();
        renderCart(); updateCartCount(); hydrateButtonsFromCart();
        window.dispatchEvent(new Event("cart:changed"));
        window.addEventListener("cart:changed", hydrateButtonsFromCart);
        initialSync();
    });
})();
