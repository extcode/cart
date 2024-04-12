import { dispatchCustomEvent } from './helper/dispatch_custom_event';

document.addEventListener('DOMContentLoaded', () => {
  function setValue (parentElement, targetElementClass, value) {
    const targetElement = parentElement.querySelector(targetElementClass);
    if (targetElement) {
      targetElement.querySelector('.price').innerHTML = value;
    }
  }

  const productPrice = document.querySelector('#product-price');

  document.querySelector('#be-variants-select')
    .addEventListener('change', function changePriceAndDiscountBasedOnCustomerChoice () {
      const selectedOption = this.selectedOptions[0];
      const { specialPrice } = selectedOption.dataset;
      const { regularPrice } = selectedOption.dataset;
      const { specialPricePercentageDiscount } = selectedOption.dataset;

      setValue(productPrice, '.special_price', specialPrice);
      setValue(productPrice, '.regular_price', regularPrice);
      setValue(productPrice, '.special_price_percentage_discount', specialPricePercentageDiscount);

      dispatchCustomEvent(
        'extcode:be-variant-was-changed',
        {
          specialPrice,
          regularPrice,
          specialPricePercentageDiscount
        }
      );
    });
});
