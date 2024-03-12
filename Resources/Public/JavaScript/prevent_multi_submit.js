(() => {
  // JavaScript/prevent_multi_submit.js
  document.addEventListener("DOMContentLoaded", function() {
    function addDisableSubmitButtonListener(formId) {
      let form = document.querySelector(formId);
      if (form) {
        form.addEventListener("submit", function() {
          this.querySelector('input[type="submit"]').setAttribute("disabled", "true");
        });
      }
    }
    addDisableSubmitButtonListener("#form-cart");
    addDisableSubmitButtonListener("#form-coupon");
    addDisableSubmitButtonListener("#form-order");
  });
})();
