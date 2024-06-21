import { dispatchCustomEvent } from './helper/dispatch_custom_event';
import { createHtmlElementFromString, replaceHtmlElementByIdentifier } from './helper/html_helper';

document.addEventListener('DOMContentLoaded', () => {
  const shippingSameAsBillingElement = document.querySelector('#shipping-same-as-billing');
  const billingCountryElement = document.querySelector('#billingAddress-country');
  const shippingCountryElement = document.querySelector('#shippingAddress-country');

  function setDisabledStatus (parentElement, fieldType, disabledStatus) {
    parentElement.querySelectorAll(fieldType).forEach(
      (field) => {
        if (field.dataset.disableShipping) {
          const inputField = field;
          inputField.disabled = disabledStatus;
        }
      }
    );
  }

  /**
   * Listen to changes of cart form field for billing country.
   */
  billingCountryElement.addEventListener('change', () => {
    const billingCountry = billingCountryElement.value;
    let shippingCountry = '';

    if (!shippingSameAsBillingElement.checked) {
      shippingCountry = shippingCountryElement.value;
    }
    updateCountry(billingCountry, shippingCountry);
  });

  /**
   * Listen to changes of cart form field for shipping country.
   */
  shippingCountryElement.addEventListener('change', () => {
    const billingCountry = billingCountryElement.value;
    const shippingCountry = shippingCountryElement.value;

    updateCountry(billingCountry, shippingCountry);
  });

  /**
   * Listen to changes of cart form field whether shipping address is same as billing address.
   */
  shippingSameAsBillingElement.addEventListener('change', function () {
    const stepShippingAddressElement = document.querySelector('#checkout-step-shipping-address');
    if (shippingSameAsBillingElement.checked) {
      stepShippingAddressElement.style.display = 'none';
    } else {
      stepShippingAddressElement.style.display = null;
    }

    const billingCountry = billingCountryElement.value;
    // Shipping costs shall depend on billing country if shipping address == billing address.
    // Due to this the value of the shipping country is only considered if the shipping address
    // differs from the billing address.
    const shippingCountry = shippingSameAsBillingElement.checked
      ? billingCountryElement.value
      : shippingCountryElement.value;

    // Disable shipping fields if shipping address == billing address.
    const disabledStatus = shippingSameAsBillingElement.checked;

    setDisabledStatus(stepShippingAddressElement, 'input', disabledStatus);
    setDisabledStatus(stepShippingAddressElement, 'select', disabledStatus);

    updateCountry(billingCountry, shippingCountry);
  });

  /**
   * Collect data from form fields,
   * make an AJAX request and replace elements of the form with the incoming response.
   */
  function updateCountry (billingCountry, shippingCountry) {
    const formData = new FormData();
    formData.append('tx_cart_cart[shipping_same_as_billing]', shippingSameAsBillingElement.checked);
    formData.append('tx_cart_cart[billing_country]', billingCountry);
    formData.append('tx_cart_cart[shipping_country]', shippingCountry);

    const actionUrl = document.querySelector('[data-ajax-update-country-action-url]').dataset.ajaxUpdateCountryActionUrl;

    fetch(actionUrl, {
      method: 'POST',
      body: formData
    })
      .then((response) => response.text())
      .then((response) => {
        const responseAsHtml = createHtmlElementFromString(response);

        replaceHtmlElementByIdentifier(responseAsHtml, '#form-cart');
        replaceHtmlElementByIdentifier(responseAsHtml, '#checkout-step-shipping-method');
        replaceHtmlElementByIdentifier(responseAsHtml, '#checkout-step-payment-method');
        replaceHtmlElementByIdentifier(responseAsHtml, '#checkout-step-summary');

        dispatchCustomEvent(
          'extcode:country-updated',
          {
            response
          }
        );
      });
  }
});
