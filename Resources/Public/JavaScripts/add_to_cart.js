document.addEventListener('DOMContentLoaded', function() {
    const addToCartForms = document.querySelectorAll('[data-ajax=\'1\']');

    addToCartForms.forEach(function(addToCartForm, index) {
        addToCartForm.addEventListener("submit", function(event) {
            var data = this;

            fetch(data.getAttribute('action'), {
                method: data.getAttribute('method'),
                body: new FormData(data)
            })
                .then(res => res.text())
                .then(function (response) {
                    showSuccessMessageBlock(data, response);
                    updateMiniCart(data, response);
                });

            event.preventDefault();
        });
    });
});

function showSuccessMessageBlock(form, data) {
    var messageBlock;
    var messageTimeout = form.querySelector('[data-ajax-message-timeout]').querySelector('ajax-message-timeout');

    if (!messageTimeout) {
        messageTimeout = 3000;
    }

    var response = JSON.parse(data);
    if (response.status === "200") {
        form.querySelector('[data-ajax-success-message]').innerHTML = response.messageBody;
        form.querySelector('[data-ajax-success-block]').style.display = 'block';
        window.setTimeout(function () {
            form.querySelector('[data-ajax-success-block]').style.display = 'none';
        }, messageTimeout);
    } else {
        form.querySelector('[data-ajax-error-message]').innerHTML = response.messageBody;
        form.querySelector('[data-ajax-error-block]').style.display = 'block';
        window.setTimeout(function () {
            form.querySelector('[data-ajax-error-block]').style.display = 'none';
        }, messageTimeout);
    }
}

function updateMiniCart(form, data) {
    var response = JSON.parse(data);
    if (response.status === "200") {
        //$("#cart-preview .cart-preview-count").html(response.count);
        //$("#cart-preview .net").html(response.net);
        //$("#cart-preview .gross").html(response.gross);

        //if (response.count > 0) {
        //    $("#cart-preview .checkout-link").show();
        //} else {
        //    $("#cart-preview .checkout-link").hide();
        //}

        //$(document).trigger("status.cartWasChanged", [true]);

        //form.each(function () {
        //    this.reset();
        //});
    }
}