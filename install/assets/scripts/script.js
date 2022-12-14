let isOrderPage = document.querySelector('#bx-soa-order');

if (isOrderPage != null) {
    let sl3wMinPriceOrderPopupInterval = setInterval(function () {
        let bxErrorDanger = document.querySelector('#bx-soa-order #sl3w_minpriceorder__text');

        if (bxErrorDanger) {
            sl3wOpenMinPricePopup(bxErrorDanger.innerHTML);
            clearInterval(sl3wMinPriceOrderPopupInterval);
        }
    }, 1000);

    function sl3wOpenMinPricePopup(popupText) {
        let popupBg = document.querySelector('.sl3w-min-price_popup__bg');

        if (popupBg === null) {
            let popupDiv = document.createElement('div');

            popupDiv.className = 'sl3w-min-price_popup__bg';
            popupDiv.innerHTML = '<div class="sl3w-min-price__popup">' +
                '<div class="sl3w-min-price__close-popup"></div>' +
                '<div class="sl3w-min-price__popup-content">'
                + popupText +
                '</div>' +
                '</div>';

            document.body.appendChild(popupDiv);

            popupBg = popupDiv;
        }

        let closePopupButton = document.querySelector('.sl3w-min-price__close-popup');

        closePopupButton.addEventListener('click', function () {
            popupBg.classList.remove('active');
        });

        document.addEventListener('click', function (e) {
            if (e.target === popupBg) {
                popupBg.classList.remove('active');
            }
        });

        popupBg.classList.add('active');
    }
}