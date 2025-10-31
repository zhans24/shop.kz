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

        // 1) очищаем корзину (localStorage)
        try { localStorage.removeItem(CART_KEY); } catch (e) {}

        // 2) дергаем событие, чтобы прочие виджеты пересчитались (если слушают cart:changed)
        try { window.dispatchEvent(new Event('cart:changed')); } catch(e) {}

        // 3) обновляем бейдж в шапке (если есть .basket-link)
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

        // 4) показываем тост
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
