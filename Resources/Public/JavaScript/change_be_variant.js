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

  // JavaScript/change_be_variant.js
  document.addEventListener("DOMContentLoaded", () => {
    function setValue(parentElement, targetElementClass, spanClass, value) {
      const targetElement = parentElement.querySelector(targetElementClass);
      if (targetElement) {
        const spanElement = targetElement.querySelector(spanClass);
        if (typeof value === "undefined") {
          spanElement.innerHTML = "&nbsp;";
          return;
        }
        if (spanClass !== ".stock") {
          spanElement.innerHTML = value;
        } else {
          const template = Number(value) === 1 ? spanElement.dataset.stockSingular : spanElement.dataset.stockPlural;
          const placeholder = "%1$s";
          const text = template.replace(placeholder, value);
          spanElement.innerHTML = text;
        }
      }
    }
    const productPrice = document.querySelector("#product-price");
    document.querySelector("#be-variants-select").addEventListener("change", function changePriceAndDiscountBasedOnCustomerChoice() {
      const selectedOption = this.selectedOptions[0];
      const { specialPrice } = selectedOption.dataset;
      const { regularPrice } = selectedOption.dataset;
      const { specialPricePercentageDiscount } = selectedOption.dataset;
      const { availableStock } = selectedOption.dataset;
      setValue(productPrice, ".special_price", ".price", specialPrice);
      setValue(productPrice, ".regular_price", ".price", regularPrice);
      setValue(productPrice, ".special_price_percentage_discount", ".price", specialPricePercentageDiscount);
      setValue(productPrice, ".available_stock", ".stock", availableStock);
      dispatchCustomEvent(
        "extcode:be-variant-was-changed",
        {
          specialPrice,
          regularPrice,
          specialPricePercentageDiscount
        }
      );
    });
  });
})();
