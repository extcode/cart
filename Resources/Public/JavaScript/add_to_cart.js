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

  // JavaScript/add_to_cart.js
  document.addEventListener("DOMContentLoaded", function() {
    const addToCartForms = document.querySelectorAll("[data-ajax='1']");
    addToCartForms.forEach(function(addToCartForm, index) {
      addToCartForm.addEventListener("submit", function(event) {
        event.preventDefault();
        const data = this;
        const actionUrl = data.getAttribute("action");
        fetch(actionUrl, {
          method: data.getAttribute("method"),
          body: new FormData(data)
        }).then((response2) => response2.text()).then((response2) => {
          renderAddToCartResultMessage2(data, response2);
          updateMiniCart2(data, response2);
        });
      });
    });
    function renderAddToCartResultMessage2(form, data) {
      let messageTimeout = parseInt(form.querySelector("[data-ajax-message-timeout]").dataset["ajaxMessageTimeout"]);
      if (!messageTimeout) {
        messageTimeout = 3e3;
      }
      let response2 = JSON.parse(data);
      if (response2.status === "200") {
        const successContainer = form.querySelector("[data-ajax-success-block]");
        const successElement = form.querySelector("[data-ajax-success-message]");
        successElement.innerHTML = response2.messageBody;
        successContainer.style.display = null;
        fadeOut(successContainer, messageTimeout);
        dispatchCustomEvent(
          "render-add-to-cart-result-message",
          {
            response: response2,
            success: true,
            element: successElement
          }
        );
      } else {
        const errorContainer = form.querySelector("[data-ajax-error-block]");
        const errorElement = form.querySelector("[data-ajax-error-message]");
        errorElement.innerHTML = response2.messageBody;
        errorContainer.style.display = null;
        fadeOut(errorContainer, messageTimeout);
        dispatchCustomEvent(
          "render-add-to-cart-result-message",
          {
            response: response2,
            success: false,
            element: errorElement
          }
        );
      }
    }
    function fadeOut(element, messageTimeout) {
      const transitionTime = 200;
      element.style.transition = "opacity " + transitionTime + "ms ease";
      window.setTimeout(function() {
        element.style.opacity = 0;
      }, messageTimeout);
      window.setTimeout(
        function() {
          element.style.transition = "unset";
          element.style.display = "none";
          element.style.opacity = 1;
        },
        messageTimeout + transitionTime
      );
      dispatchCustomEvent(
        "hide-message-block",
        {
          element
        }
      );
    }
    function updateMiniCart2(form, data) {
      let response2 = JSON.parse(data);
      if (response2.status !== "200") {
        return;
      }
      let count = response2.count;
      let net = response2.net;
      let gross = response2.gross;
      let miniCart = document.querySelector("#cart-preview");
      let countElement = miniCart.querySelector(".cart-preview-count");
      let netElement = miniCart.querySelector(".net");
      let grossElement = miniCart.querySelector(".gross");
      let linkElement = miniCart.querySelector(".checkout-link");
      if (countElement) {
        countElement.innerHTML = count;
      }
      if (netElement) {
        netElement.innerHTML = net;
      }
      if (grossElement) {
        grossElement.innerHTML = gross;
      }
      if (linkElement) {
        if (count > 0) {
          linkElement.style.display = "block";
        } else {
          linkElement.style.display = "none";
        }
      }
      dispatchCustomEvent(
        "minicart-was-updated",
        {
          count: response2.count,
          net: response2.net,
          gross: response2.gross
        }
      );
      document.querySelectorAll("form").forEach(function(formElement) {
        formElement.reset();
      });
    }
  });
  document.addEventListener("DOMContentLoaded", function() {
    let addToCartButton = document.querySelector('[data-add-to-cart="form"]');
    let formContainer = document.querySelector('[data-add-to-cart="result"]');
    if (!addToCartButton || !formContainer) {
      return;
    }
    disableDefaultAddToCartButFetchFormInstead();
    function disableDefaultAddToCartButFetchFormInstead() {
      addToCartButton.addEventListener("click", function(event) {
        event.preventDefault();
        const actionUrl = this.getAttribute("href");
        fetchAndInsertFormContent(actionUrl);
      });
    }
    function fetchAndInsertFormContent(actionUrl) {
      fetch(actionUrl).then((response2) => response2.text()).then((response2) => {
        formContainer.innerHTML = response2;
      });
      let forms = document.querySelectorAll("[data-add-to-cart-uri]");
      forms.forEach(function(form) {
        form.addEventListener("submit", function(event) {
          event.preventDefault();
          const actionUrl2 = form.getAttribute("data-add-to-cart-uri");
          const submitButton = form.querySelector('button[type="submit"]');
          const data = this;
          let formData = new FormData(data);
          formData.append(submitButton.getAttribute("name"), submitButton.getAttribute("value"));
          fetch(actionUrl2, {
            method: "POST",
            body: formData
          }).then((response2) => response2.text()).then(function(data2) {
            renderAddToCartResultMessage(data2, response);
            updateMiniCart(data2, response);
          });
        });
      });
    }
  });
})();
