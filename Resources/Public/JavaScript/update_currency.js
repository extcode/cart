(() => {
  // JavaScript/helper/dispatch_custom_event.js
  function dispatchCustomEvent(name, dataObject) {
    const customEvent = new CustomEvent(
      `${name}`,
      {
        bubbles: true,
        cancelable: true,
        detail: dataObject
      }
    );
    document.dispatchEvent(customEvent);
  }

  // JavaScript/helper/html_helper.js
  function createHtmlElementFromString(text) {
    const tempWrapper = document.createElement("div");
    tempWrapper.innerHTML = text;
    return tempWrapper;
  }
  function findParentBySelector(element, parentSelector) {
    if (element.parentElement.tagName.toLowerCase() === parentSelector.toLowerCase()) {
      return element.parentElement;
    } else {
      return findParentBySelector(element.parentElement, parentSelector);
    }
  }
  function replaceHtmlElementByIdentifier(responseAsHtml, identifier) {
    let existingElement = document.querySelector(identifier);
    if (!existingElement)
      return;
    let newElement = responseAsHtml.querySelector(identifier);
    existingElement.parentNode.replaceChild(newElement, existingElement);
  }

  // JavaScript/update_currency.js
  document.addEventListener("DOMContentLoaded", function() {
    function updateCurrency(currencyCode, actionUrl, reloadOnly = false) {
      let formData = new FormData();
      formData.append("tx_cart_cart[currencyCode]", currencyCode);
      fetch(actionUrl, {
        method: "POST",
        body: formData
      }).then((response) => response.text()).then((response) => {
        if (reloadOnly) {
          location.reload();
        } else {
          const responseAsHtml = createHtmlElementFromString(response);
          replaceHtmlElementByIdentifier(responseAsHtml, "#form-cart");
          replaceHtmlElementByIdentifier(responseAsHtml, "#checkout-step-shipping-method");
          replaceHtmlElementByIdentifier(responseAsHtml, "#checkout-step-payment-method");
          replaceHtmlElementByIdentifier(responseAsHtml, "#checkout-step-coupon");
          replaceHtmlElementByIdentifier(responseAsHtml, "#checkout-step-summary");
        }
        dispatchCustomEvent(
          "extcode:currency-updated",
          {
            response
          }
        );
      });
    }
    let cartCurrencySelector = document.querySelector(".cart-currency-selector");
    if (cartCurrencySelector) {
      cartCurrencySelector.addEventListener("change", function() {
        const form = findParentBySelector(this, "form");
        updateCurrency(this.value, form.getAttribute("action"));
      });
    }
    let currencySelector = document.querySelector(".currency-selector");
    if (currencySelector) {
      currencySelector.addEventListener("change", function() {
        const form = findParentBySelector(this, "form");
        updateCurrency(this.value, form.getAttribute("action"));
      });
    }
  });
})();
