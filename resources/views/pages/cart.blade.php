@php use App\Models\DeliveryMethod;use App\Models\PaymentMethod; @endphp
@extends('layouts.front')

@section('title','Корзина')

@section('content')
    <main class="pages">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Хлебные крошки">
                <ol>
                    <li><a href="{{ route('front.home') }}">Главная</a></li>
                    <li aria-current="page">Корзина</li>
                </ol>
            </nav>
        </div>

        <div class="centeres">
            <section class="contacts-page">
                <span class="decor-text">Создаем комфорт</span>
            </section>

            <section class="cart">
                <div class="container">
                    <div class="cart__main">
                        <div class="cart__content">
                            <div class="cart__info">
                                <h2 class="cart__title">Корзина</h2>
                                <ul class="cart__items"></ul>

                                {{-- server-side ошибка оформления --}}
                                @if ($errors->has('checkout'))
                                    <div class="alert alert-danger" style="margin-top:12px;">
                                        {{ $errors->first('checkout') }}
                                    </div>
                                @endif
                            </div>

                            <div class="cart__form">
                                <h3 class="cart__form-title">Форма оформления заказа</h3>

                                <form class="cart__order-form" id="checkoutForm" method="post" action="{{ route('checkout.submit') }}">
                                    @csrf
                                    <input type="hidden" name="client_total" id="clientTotal">
                                    <input type="hidden" name="items" id="clientItemsJson">

                                    <div class="cart__row">
                                        <label>Выбор доставки:</label>
                                        <div class="cart__options">
                                            @php $delivery = DeliveryMethod::active()->orderBy('id')->get(); @endphp
                                            @forelse($delivery as $i => $dm)
                                                <input
                                                    type="radio"
                                                    id="delivery{{ $dm->id }}"
                                                    name="delivery_method_id"
                                                    value="{{ $dm->id }}"
                                                    data-price="{{ (float)$dm->price }}"
                                                    {{ $i===0?'checked':'' }}
                                                    hidden
                                                >
                                                <label for="delivery{{ $dm->id }}" class="option-btn">{{ $dm->name }}</label>
                                            @empty
                                                <span style="font-size:14px;color:#999;">Нет активных способов доставки</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="cart__row">
                                        <label>Контактное лицо:</label>
                                        <div class="cart__options">
                                            <input type="radio" id="person1" name="customer_type" value="private" checked hidden>
                                            <label for="person1" class="option-btn">Частное лицо</label>

                                            <input type="radio" id="person2" name="customer_type" value="company" hidden>
                                            <label for="person2" class="option-btn">Юридическое лицо</label>
                                        </div>
                                    </div>

                                    <div class="cart__row">
                                        <label>Имя</label>
                                        <input type="text" name="contact_name" class="cart__input" placeholder="Ваше имя" required>
                                    </div>

                                    <div class="cart__row">
                                        <label>Телефон</label>
                                        <input type="tel" id="phone" name="phone" class="cart__input" placeholder="+7 747 123 45 67" required>
                                    </div>

                                    <div class="cart__row">
                                        <label>Адрес доставки</label>
                                        <input type="text" name="address" class="cart__input" placeholder="Абая, 34">
                                    </div>

                                    <div class="cart__row">
                                        <label>Способ оплаты:</label>
                                        <div class="cart__options">
                                            @php $payments = PaymentMethod::active()->orderBy('id')->get(); @endphp
                                            @forelse($payments as $j => $pm)
                                                <input type="radio" id="pay{{ $pm->id }}" name="payment_method_id" value="{{ $pm->id }}" {{ $j===0?'checked':'' }} hidden>
                                                <label for="pay{{ $pm->id }}" class="option-btn">{{ $pm->name }}</label>
                                            @empty
                                                <span style="font-size:14px;color:#999;">Нет активных способов оплаты</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    <button type="submit" class="cart__submit">Подтвердить</button>
                                </form>
                            </div>
                        </div>

                        <aside class="cart__aside">
                            <div class="cart__summary">
                                <h3>Заказ</h3>
                                <p class="top-cart">Товары: <span data-cart-count>0 шт</span></p>
                                <p class="top-cart">Контактное лицо: <span data-cart-person>Частное лицо</span></p>
                                <p class="top-cart">Доставка: <span data-cart-delivery>-</span></p>
                                <p class="top-cart">Оплата: <span data-cart-payment>-</span></p>
                                <hr>

                                <h4 class="second-title">Итого:</h4>
                                <p class="bottom-cart">
                                    <span>Итоговая сумма</span>
                                    <span data-cart-subtotal-old>0 ₸</span>
                                </p>
                                <p class="bottom-cart">
                                    <span>Скидка</span>
                                    <span data-cart-discount>0 ₸</span>
                                </p>
                                <p class="bottom-cart bottom-cart-last">
                                    <span>Доставка</span>
                                    <span data-cart-shipping>0 ₸</span>
                                </p>
                                <hr>
                                <h5>Итоговая оплата: <span data-cart-total>0 ₸</span></h5>
                                <button class="cart__btn" id="asideSubmit">Заказать</button>
                            </div>
                        </aside>
                    </div>
                </div>
            </section>
        </div>
    </main>

    {{-- Пересчёт и отправка --}}
    <script>
        (function () {
            const CART_KEY = 'cartItems';

            const parsePrice = (t) => Number((t ?? '').toString().replace(/[^\d]/g,'') || 0);
            const getCart    = () => { try { return JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch { return []; } };
            const fmt = (n) => new Intl.NumberFormat('ru-RU').format(Math.round(Number(n) || 0)).replace(/\u00A0/g,' ') + ' ₸';
            const setText    = (sel, val='') => { const el=document.querySelector(sel); if(el) el.textContent=val; };

            function recalc() {
                const items = getCart();

                const delInput = document.querySelector('input[name="delivery_method_id"]:checked');
                const ship = delInput ? Math.round(Number(delInput.dataset.price || 0)) : 0;
                let delName = '-';
                if (delInput) {
                    const lbl = document.querySelector('label[for="'+delInput.id+'"]');
                    if (lbl) delName = lbl.textContent.trim();
                }

                const payInput = document.querySelector('input[name="payment_method_id"]:checked');
                let payName = '-';
                if (payInput) {
                    const lbl = document.querySelector('label[for="'+payInput.id+'"]');
                    if (lbl) payName = lbl.textContent.trim();
                }

                if (!items.length) {
                    setText('[data-cart-count]','0 шт');
                    setText('[data-cart-subtotal-old]','0 ₸');
                    setText('[data-cart-discount]','0 ₸');
                    setText('[data-cart-total]','0 ₸');
                    setText('[data-cart-delivery]', delName);
                    setText('[data-cart-payment]',  payName);
                    setText('[data-cart-shipping]', '0 ₸');
                    const hTotal=document.getElementById('clientTotal');
                    const hItems=document.getElementById('clientItemsJson');
                    if (hTotal) hTotal.value='';
                    if (hItems) hItems.value='[]';
                    return;
                }

                const count    = items.reduce((s,i)=>s+(+i.qty||0),0);

                let oldTotal=0, newTotal=0;
                for (const i of items) {
                    const qty  = +i.qty || 0;
                    const newP = parsePrice(i.newPrice);
                    const oldP = parsePrice(i.oldPrice) || newP;
                    newTotal += newP * qty;
                    oldTotal += oldP * qty;
                }
                const discount = Math.max(0, oldTotal - newTotal);
                const grandTotal = newTotal + ship;

                setText('[data-cart-count]', count ? (count+' шт') : '0 шт');
                setText('[data-cart-delivery]', delName);
                setText('[data-cart-payment]',  payName);
                setText('[data-cart-subtotal-old]', fmt(oldTotal));
                setText('[data-cart-discount]',    fmt(discount));
                setText('[data-cart-shipping]',    fmt(ship));
                setText('[data-cart-total]',       fmt(grandTotal));

                const person = document.querySelector('input[name="customer_type"]:checked');
                setText('[data-cart-person]', person && person.value==='company' ? 'Юридическое лицо' : 'Частное лицо');

                const hTotal=document.getElementById('clientTotal');
                const hItems=document.getElementById('clientItemsJson');
                if (hTotal) hTotal.value = String(Math.round(grandTotal));
                if (hItems) hItems.value = JSON.stringify(items.map(i => ({ code:String(i.code||''), qty:+i.qty||0 })));
            }

            document.addEventListener('change', (e)=>{
                if (e.target.matches('input[name="customer_type"], input[name="payment_method_id"], input[name="delivery_method_id"]')) recalc();
            });

            window.addEventListener('cart:changed', recalc);

            const asideBtn=document.getElementById('asideSubmit');
            if (asideBtn) asideBtn.addEventListener('click', (e)=>{
                recalc();
                const items = getCart();
                if (!items.length) {
                    e.preventDefault();
                    (window.__cartShowToast || alert)('Корзина пуста. Добавьте товары.','error');
                    return;
                }
                document.getElementById('checkoutForm').requestSubmit();
            });

            const form=document.getElementById('checkoutForm');
            if (form) form.addEventListener('submit', (e)=>{
                recalc();
                const items = getCart();
                if (!items.length) {
                    e.preventDefault();
                    (window.__cartShowToast || alert)('Корзина пуста. Добавьте товары.','error');
                }
            });

            // первичный рендер/пересчёт
            recalc();
        })();
    </script>

    {{-- Тост при ошибках валидации/сервера --}}
    @if ($errors->any())
        <script>
            window.addEventListener('DOMContentLoaded', ()=>{
                const err = @json($errors->first('checkout') ?: $errors->first());
                (window.__cartShowToast || alert)(err || 'Заказ не отправлен: ошибка на сервере.','error');
            });
        </script>
    @endif

    {{-- Успех: очищаем корзину и показываем зелёный тост (аналогично твоему) --}}
    @if (session('ok'))
        <style>
            .toast-success{
                position:fixed;right:20px;top:20px;z-index:9999;background:#12B76A;color:#fff;
                padding:14px 16px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.15);
                font-size:14px;line-height:1.3;display:flex;gap:10px;align-items:center;
                opacity:0;transform:translateY(-8px);transition:opacity .25s ease, transform .25s ease;
            }
            .toast-success.show{opacity:1;transform:translateY(0)}
            .toast-success .toast-close{margin-left:8px;background:rgba(255,255,255,.2);border:0;color:#fff;width:28px;height:28px;border-radius:8px;cursor:pointer}
        </style>

        <div id="toastSuccess" class="toast-success" role="status" aria-live="polite">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M20 7L9 18L4 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ session('ok') }}</span>
            <button type="button" class="toast-close" aria-label="Закрыть">×</button>
        </div>

        <script>
            (function () {
                const CART_KEY = 'cartItems';

                try { localStorage.removeItem(CART_KEY); } catch (e) {}
                try { window.dispatchEvent(new Event('cart:changed')); } catch(e) {}

                // обновим бейдж в шапке
                (function updateCartBadge(){
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
                    badge.textContent = '0';
                })();

                const toast = document.getElementById('toastSuccess');
                if (toast) {
                    requestAnimationFrame(()=> toast.classList.add('show'));
                    const closer = toast.querySelector('.toast-close');
                    let hideTimer = setTimeout(hide, 5000);
                    function hide(){ toast.classList.remove('show'); clearTimeout(hideTimer); }
                    closer && closer.addEventListener('click', hide);
                }
            })();
        </script>
    @endif
@endsection
