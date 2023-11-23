document.addEventListener('DOMContentLoaded', function() {
    const addToCartForms = document.querySelectorAll('[data-ajax=\'1\']');

    addToCartForms.forEach(function(addToCartForm, index){
        addToCartForm.addEventListener("submit", function(event) {
            event.preventDefault();

            const data = this;
            const actionUrl = data.getAttribute('action');

            fetch(actionUrl, {
                method: data.getAttribute('method'),
                body: new FormData(data)
            })
                .then(response => response.text())
                .then(response => {
                    showMessageBlock(data, response);
                    updateMiniCart(data, response);
                });
        });
    });

    function showMessageBlock(form, data) {
        let messageTimeout = parseInt(form.querySelector('[data-ajax-message-timeout]').dataset['ajaxMessageTimeout']);

        if (!messageTimeout) {
            messageTimeout = 3000;
        }

        let response = JSON.parse(data);
        if (response.status === "200") {
            const successContainer = form.querySelector('[data-ajax-success-block]');
            const successElement = form.querySelector('[data-ajax-success-message]');

            successElement.innerHTML = response.messageBody;
            successContainer.style.display = null;
            fadeOut(successContainer, messageTimeout);
        } else {
            const errorContainer = form.querySelector('[data-ajax-error-block]');
            const errorElement = form.querySelector('[data-ajax-error-message]');
            errorElement.innerHTML = response.messageBody;
            errorContainer.style.display = null;
            fadeOut(errorContainer, messageTimeout);
        }
    }

    /**
     * Use CSS for fade-out (is much more performant than JS)
     * @param element HTML element to fade out
     * @param messageTimeout time after which the effect starts
     */
    function fadeOut(element, messageTimeout) {
        const transitionTime = 200;
        element.style.transition = 'opacity ' + transitionTime + 'ms ease';

        // the fade out
        window.setTimeout(function() {
            element.style.opacity = 0;
        }, messageTimeout);

        // reset element after fade out
        window.setTimeout(function() {
                element.style.transition = 'unset';
                element.style.display = 'none';
                element.style.opacity = 1;
            },
            messageTimeout + transitionTime
        );
    }

    function updateMiniCart(form, data) {
        let response = JSON.parse(data);
        if (response.status !== "200") {
            return;
        }

        let count = response.count;
        let net = response.net;
        let gross = response.gross;

        let miniCart = document.querySelector('#cart-preview')
        let countElement = miniCart.querySelector('.cart-preview-count');
        let netElement = miniCart.querySelector('.net');
        let grossElement = miniCart.querySelector('.gross');
        let linkElement = miniCart.querySelector('.checkout-link');

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
                linkElement.style.display = 'block';
            } else {
                linkElement.style.display = 'none';
            }
        }

        // TODO: What's the meaning of that?
        //$(document).trigger("status.cartWasChanged", [true]);

        document.querySelectorAll('form').forEach(function(formElement) {
            formElement.reset();
        })
    }

});


