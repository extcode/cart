document.addEventListener('DOMContentLoaded', function () {

    // Todo: Ask DG:
    //   There is no element with HTML attribute `data-add-to-cart`. What is it for?
    //   even in the original commit is not element for that, see
    //   https://github.com/extcode/cart/commit/0a0e3b66e3216b185de5652c7927a95f765da162#diff-affc437e2360eda6a0971518e53d77069bb948b5251c0b88c41a5cd010ef2822
    //   and neither in `cart_events`
    //   https://github.com/extcode/cart_events/commit/7689f723f36d8dcc484d0141cec144898f1be73a
    document.querySelector('[data-add-to-cart="form"]').addEventListener('click', function (event) {
        event.preventDefault();
        const actionUrl = this.getAttribute('href');

        fetch(actionUrl)
            .then(response => res.text())
            .then(response => {
                document.querySelector('[data-add-to-cart="result"]').innerHTML = reponse;
            })
    })

    let forms = document.querySelectorAll('[data-add-to-cart-uri]');
    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            form.removeEventListener('submit', submitHandler);

            let url = form.getAttribute('data-add-to-cart-uri');
            let data = new FormData(form);
            let submitButton = form.querySelector('button[type="submit"]');

            data.append(submitButton.getAttribute('name'), submitButton.getAttribute('value'));

            fetch(url, {
                method: 'POST',
                body: data,
            })
                .then(response => response.text())
                .then(function (data) {
                    handleAddToCartSuccessResponse(form, data)
                });
        });

        function submitHandler(e) {
            e.preventDefault();
            form.removeEventListener('submit', submitHandler);
        }
    });
});
