(() => {
  // JavaScript/helper/dispatch_custom_event.js
  function dispatchCustomEvent(name, dataObject) {
    const customEvent = new CustomEvent(
      `extcode:${name}`,
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
  function replaceHtmlElementByIdentifier(responseAsHtml, identifier) {
    let existingElement = document.querySelector(identifier);
    if (!existingElement)
      return;
    let newElement = responseAsHtml.querySelector(identifier);
    existingElement.parentNode.replaceChild(newElement, existingElement);
  }

  // JavaScript/update_payment_shipping.js
  document.addEventListener("DOMContentLoaded", function() {
    function setAjaxEventListener(parentId, targetClass) {
      document.querySelector(parentId).parentElement.addEventListener("click", function(event) {
        if (!event.target.classList.contains(targetClass)) {
          return;
        }
        event.preventDefault();
        const actionUrl = event.target.getAttribute("href");
        fetch(actionUrl, {
          method: "GET"
        }).then((response) => response.text()).then((response) => {
          const responseAsHtml = createHtmlElementFromString(response);
          replaceHtmlElementByIdentifier(responseAsHtml, "#checkout-step-shipping-method");
          replaceHtmlElementByIdentifier(responseAsHtml, "#checkout-step-payment-method");
          replaceHtmlElementByIdentifier(responseAsHtml, "#checkout-step-summary");
          dispatchCustomEvent(
            targetClass,
            {
              response
            }
          );
        });
      });
    }
    setAjaxEventListener("#checkout-step-payment-method", "set-payment");
    setAjaxEventListener("#checkout-step-shipping-method", "set-shipping");
  });
})();
