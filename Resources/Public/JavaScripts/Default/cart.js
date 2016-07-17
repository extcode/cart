$('#shipping_same_as_billing').change(function() {
    $('#shipping-address').toggle(!this.checked);
});

$('#be-variants-select').change(function () {
    var special_price = $(this).children().filter(':selected').data('special-price');
    var regular_price = $(this).children().filter(':selected').data('regular-price');

    $('#product-price .special_price .price').html(special_price);
    $('#product-price .regular_price .price').html(regular_price);
});

$("#add-product-form").submit(function(e) {
    $form = $(this);

    if ($form.data('remote')) {
        $.ajax({
            async: 'true',
            url: 'index.php',
            type: "POST",

            data: {
                eID: 'addProduct',
                cartPid: $('#add-product-form input[name="tx_cart_cart[pid]"]').val(),
                request: {
                    arguments: {
                        cartPid: $('#add-product-form input[name="tx_cart_cart[pid]"]').val(),
                        productId: $('#add-product-form input[name="tx_cart_cart[productId]"]').val(),
                        quantity: $('#add-product-form input[name="tx_cart_cart[quantity]"]').val(),
                        beVariants: {
                            1: $('#add-product-form select[name="tx_cart_cart[beVariants][1]"]').val(),
                            2: $('#add-product-form select[name="tx_cart_cart[beVariants][2]"]').val(),
                            3: $('#add-product-form select[name="tx_cart_cart[beVariants][3]"]').val()
                        }
                    }
                }
            },

            success: function(data)
            {
                var response = JSON.parse(data);
                if (response.status == "200") {
                    $('#tx-cart-minicart span.count').html(response.count);
                    $('#tx-cart-minicart span.net').html(response.net);
                    $('#tx-cart-minicart span.gross').html(response.gross);

                    if (response.count > 0) {
                        $('#link-to-checkout').show();
                    } else {
                        $('#link-to-checkout').hide();
                    }
                }
            }
        });

        e.preventDefault();
    }
});