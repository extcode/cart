document.addEventListener('DOMContentLoaded', function () {

    // Listen in order form for setting of payment or shipping methods
    function setAjaxEventListener(parentId, targetClass) {
        // Use parent of parentId because element of parentId itself will be replaced.
        // Listening direct for it would mean losing the EventListener.
        document.querySelector(parentId).parentElement.addEventListener('click', function (event) {
            if (!event.target.classList.contains(targetClass)) {
                return;
            }
            event.preventDefault();
            const actionUrl = event.target.getAttribute('href');

            fetch(actionUrl, {
                method: 'GET',
            })
                .then(response => response.text())
                .then(response => {
                    const responseAsHtml = createHtmlElementFromString(response);
                    replaceHtmlElementByIdentifier(responseAsHtml, '#checkout-step-shipping-method');
                    replaceHtmlElementByIdentifier(responseAsHtml, '#checkout-step-payment-method');
                    replaceHtmlElementByIdentifier(responseAsHtml, '#checkout-step-summary');

                    dispatchCustomEvent(
                        targetClass,
                        {
                            response: response,
                        }
                    );
                });

        })
    }


    function createHtmlElementFromString(text) {
        const tempWrapper = document.createElement('div');
        tempWrapper.innerHTML = text;
        return tempWrapper;
    }

    function replaceHtmlElementByIdentifier(responseAsHtml, identifier) {
        let existingElement = document.querySelector(identifier);
        if (!existingElement) return;
        let newElement = responseAsHtml.querySelector(identifier);
        existingElement.parentNode.replaceChild(newElement, existingElement);
    }

    setAjaxEventListener('#checkout-step-payment-method', 'set-payment');
    setAjaxEventListener('#checkout-step-shipping-method', 'set-shipping');


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
})
