(() => {
  // JavaScript/prevent_multi_submit.js
  document.addEventListener("DOMContentLoaded", () => {
    function addDisableSubmitButtonListener(formId) {
      const form = document.querySelector(formId);
      if (form) {
        form.addEventListener("submit", function setSubmitButtonToDisabled() {
          this.querySelector('input[type="submit"]').setAttribute("disabled", "true");
        });
      }
    }
    addDisableSubmitButtonListener("#form-cart");
    addDisableSubmitButtonListener("#form-coupon");
    addDisableSubmitButtonListener("#form-order");
  });
})();
