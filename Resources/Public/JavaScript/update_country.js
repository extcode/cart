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
  function replaceHtmlElementByIdentifier(responseAsHtml, identifier) {
    let existingElement = document.querySelector(identifier);
    if (!existingElement)
      return;
    let newElement = responseAsHtml.querySelector(identifier);
    existingElement.parentNode.replaceChild(newElement, existingElement);
  }

  // JavaScript/update_country.js
  document.addEventListener("DOMContentLoaded", function() {
    let shippingSameAsBillingElement = document.querySelector("#shipping-same-as-billing");
    let billingCountryElement = document.querySelector("#billingAddress-country");
    let shippingCountryElement = document.querySelector("#shippingAddress-country");
    function updateCountry(billingCountry, shippingCountry) {
      let formData = new FormData();
      formData.append("tx_cart_cart[shipping_same_as_billing]", shippingSameAsBillingElement.checked);
      formData.append("tx_cart_cart[billing_country]", billingCountry);
      formData.append("tx_cart_cart[shipping_country]", shippingCountry);
      const actionUrl = document.querySelector("[data-ajax-update-country-action-url]").dataset["ajaxUpdateCountryActionUrl"];
      fetch(actionUrl, {
        method: "POST",
        body: formData
      }).then((response) => response.text()).then((response) => {
        const responseAsHtml = createHtmlElementFromString(response);
        replaceHtmlElementByIdentifier(responseAsHtml, "#form-cart");
        replaceHtmlElementByIdentifier(responseAsHtml, "#checkout-step-shipping-method");
        replaceHtmlElementByIdentifier(responseAsHtml, "#checkout-step-payment-method");
        replaceHtmlElementByIdentifier(responseAsHtml, "#checkout-step-summary");
        dispatchCustomEvent(
          "extcode:country-updated",
          {
            response
          }
        );
      });
    }
    function setDisabledStatus(parentElement, fieldType, disabledStatus) {
      parentElement.querySelectorAll(fieldType).forEach(
        function(field) {
          if (field.dataset["disableShipping"]) {
            field.disabled = disabledStatus;
          }
        }
      );
    }
    billingCountryElement.addEventListener("change", function() {
      const billingCountry = billingCountryElement.value;
      let shippingCountry = "";
      if (!shippingSameAsBillingElement.checked) {
        shippingCountry = shippingCountryElement.value;
      }
      updateCountry(billingCountry, shippingCountry);
    });
    shippingCountryElement.addEventListener("change", function() {
      const billingCountry = billingCountryElement.value;
      const shippingCountry = shippingCountryElement.value;
      updateCountry(billingCountry, shippingCountry);
    });
    shippingSameAsBillingElement.addEventListener("change", function() {
      let stepShippingAddressElement = document.querySelector("#checkout-step-shipping-address");
      if (shippingSameAsBillingElement.checked) {
        stepShippingAddressElement.style.display = "none";
      } else {
        stepShippingAddressElement.style.display = null;
      }
      const billingCountry = billingCountryElement.value;
      const shippingCountry = shippingCountryElement.value;
      const disabledStatus = shippingSameAsBillingElement.checked;
      setDisabledStatus(stepShippingAddressElement, "input", disabledStatus);
      setDisabledStatus(stepShippingAddressElement, "select", disabledStatus);
      updateCountry(billingCountry, shippingCountry);
    });
  });
})();
