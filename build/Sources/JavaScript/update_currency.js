import { dispatchCustomEvent } from './helper/dispatch_custom_event';
import { createHtmlElementFromString, findParentBySelector, replaceHtmlElementByIdentifier } from './helper/html_helper';

document.addEventListener('DOMContentLoaded', () => {
  function updateCurrency (currencyCode, actionUrl, reloadOnly = false) {
    const formData = new FormData();
    formData.append('tx_cart_cart[currencyCode]', currencyCode);

    fetch(actionUrl, {
      method: 'POST',
      body: formData
    })
      .then((response) => response.text())
      .then((response) => {
        // Reload the current page
        if (reloadOnly) {
          window.location.reload();
        } else {
          const responseAsHtml = createHtmlElementFromString(response);
          replaceHtmlElementByIdentifier(responseAsHtml, '#form-cart');
          replaceHtmlElementByIdentifier(responseAsHtml, '#checkout-step-shipping-method');
          replaceHtmlElementByIdentifier(responseAsHtml, '#checkout-step-payment-method');
          replaceHtmlElementByIdentifier(responseAsHtml, '#checkout-step-coupon');
          replaceHtmlElementByIdentifier(responseAsHtml, '#checkout-step-summary');
        }

        dispatchCustomEvent(
          'extcode:currency-updated',
          {
            response
          }
        );
      });
  }

  /**
     * Listen to changes of cart form field for currency. (in `Partials/Cart/CurrencyForm.html`)
     */
  const cartCurrencySelector = document.querySelector('.cart-currency-selector');
  if (cartCurrencySelector) {
    cartCurrencySelector.addEventListener('change', function updateCurrencyOnChange () {
      const form = findParentBySelector(this, 'form');
      updateCurrency(this.value, form.getAttribute('action'));
    });
  }

  /**
     * Listen to changes of cart form field for currency. (in `Templates/Cart/Currency/Edit.html`)
     */
  const currencySelector = document.querySelector('.currency-selector');
  if (currencySelector) {
    currencySelector.addEventListener('change', function updateCurrencyOnChange () {
      const form = findParentBySelector(this, 'form');
      updateCurrency(this.value, form.getAttribute('action'));
    });
  }
});
