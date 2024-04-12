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

  // JavaScript/add_to_cart.js
  document.addEventListener("DOMContentLoaded", () => {
    function fadeOut(element, messageTimeout) {
      const elementToFadeOut = element;
      const transitionTime = 200;
      elementToFadeOut.style.transition = `opacity ${transitionTime}ms ease`;
      window.setTimeout(() => {
        elementToFadeOut.style.opacity = 0;
      }, messageTimeout);
      window.setTimeout(
        () => {
          elementToFadeOut.style.transition = "unset";
          elementToFadeOut.style.display = "none";
          elementToFadeOut.style.opacity = 1;
        },
        messageTimeout + transitionTime
      );
      dispatchCustomEvent(
        "extcode:hide-message-block",
        {
          elementToFadeOut
        }
      );
    }
    function renderAddToCartResultMessage(form, data) {
      let messageTimeout = parseInt(
        form.querySelector("[data-ajax-message-timeout]").dataset.ajaxMessageTimeout,
        10
      );
      if (!messageTimeout) {
        messageTimeout = 3e3;
      }
      const response = JSON.parse(data);
      if (response.status === "200") {
        const successContainer = form.querySelector("[data-ajax-success-block]");
        const successElement = form.querySelector("[data-ajax-success-message]");
        successElement.innerHTML = response.messageBody;
        successContainer.style.display = null;
        fadeOut(successContainer, messageTimeout);
        dispatchCustomEvent(
          "extcode:render-add-to-cart-result-message",
          {
            response,
            success: true,
            element: successElement
          }
        );
      } else {
        const errorContainer = form.querySelector("[data-ajax-error-block]");
        const errorElement = form.querySelector("[data-ajax-error-message]");
        errorElement.innerHTML = response.messageBody;
        errorContainer.style.display = null;
        fadeOut(errorContainer, messageTimeout);
        dispatchCustomEvent(
          "extcode:render-add-to-cart-result-message",
          {
            response,
            success: false,
            element: errorElement
          }
        );
      }
    }
    function updateMiniCart(form, data) {
      const response = JSON.parse(data);
      if (response.status !== "200") {
        return;
      }
      const { count } = response;
      const { net } = response;
      const { gross } = response;
      const miniCart = document.querySelector("#cart-preview");
      const countElement = miniCart.querySelector(".cart-preview-count");
      const netElement = miniCart.querySelector(".net");
      const grossElement = miniCart.querySelector(".gross");
      const linkElement = miniCart.querySelector(".checkout-link");
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
        "extcode:minicart-was-updated",
        {
          count: response.count,
          net: response.net,
          gross: response.gross
        }
      );
      document.querySelectorAll("form").forEach((formElement) => {
        formElement.reset();
      });
    }
    const addToCartForms = document.querySelectorAll("[data-ajax='1']");
    addToCartForms.forEach((addToCartForm) => {
      addToCartForm.addEventListener("submit", function addProductToCart(event) {
        event.preventDefault();
        const data = this;
        const actionUrl = data.getAttribute("action");
        fetch(actionUrl, {
          method: data.getAttribute("method"),
          body: new FormData(data)
        }).then((response) => response.text()).then((response) => {
          renderAddToCartResultMessage(data, response);
          updateMiniCart(data, response);
        });
      });
    });
    const addToCartButton = document.querySelector('[data-add-to-cart="form"]');
    const formContainer = document.querySelector('[data-add-to-cart="result"]');
    function fetchAndInsertFormContent(actionUrl) {
      fetch(actionUrl).then((response) => response.text()).then((response) => {
        formContainer.innerHTML = response;
      });
      const forms = document.querySelectorAll("[data-add-to-cart-uri]");
      forms.forEach((form) => {
        form.addEventListener("submit", function addProductToCart(event) {
          event.preventDefault();
          const newActionUrl = form.getAttribute("data-add-to-cart-uri");
          const submitButton = form.querySelector('button[type="submit"]');
          const newData = this;
          const formData = new FormData(newData);
          formData.append(
            submitButton.getAttribute("name"),
            submitButton.getAttribute("value")
          );
          fetch(newActionUrl, {
            method: "POST",
            body: formData
          }).then((response) => response.text()).then((response) => {
            renderAddToCartResultMessage(newData, response);
            updateMiniCart(newData, response);
          });
        });
      });
    }
    function disableDefaultAddToCartButFetchFormInstead() {
      addToCartButton.addEventListener("click", function showForm(event) {
        event.preventDefault();
        const actionUrl = this.getAttribute("href");
        fetchAndInsertFormContent(actionUrl);
      });
    }
    if (!addToCartButton || !formContainer) {
      return;
    }
    disableDefaultAddToCartButFetchFormInstead();
  });
})();
