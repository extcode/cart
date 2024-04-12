document.addEventListener('DOMContentLoaded', () => {
  /**
     * Disable form to prevent double submit requests
     */
  function addDisableSubmitButtonListener (formId) {
    const form = document.querySelector(formId);
    if (form) {
      form.addEventListener('submit', function setSubmitButtonToDisabled () {
        this.querySelector('input[type="submit"]').setAttribute('disabled', 'true');
      });
    }
  }

  addDisableSubmitButtonListener('#form-cart');
  addDisableSubmitButtonListener('#form-coupon');
  addDisableSubmitButtonListener('#form-order');
});
