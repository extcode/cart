$('#shipping_same_as_billing').change(function() {
    $('#shipping-address').toggle(!this.checked);
});

$('#be-variants-select').change(function () {
    var special_price = $(this).children().filter(':selected').data('special-price');
    var regular_price = $(this).children().filter(':selected').data('regular-price');

    $('#product-price .special_price .price').html(special_price);
    $('#product-price .regular_price .price').html(regular_price);
});

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

$("#add-product-form").submit(function(e) {
    $form = $(this);
    var serializedObject = $form.serializeObject();
    serializedObject.eID = 'addProduct';
    serializedObject.cartPid = $('#add-product-form input[name="tx_cart_cart[pid]"]').val();

    if ($form.data('remote')) {
        $.ajax({
            async: 'true',
            url: 'index.php',
            type: "POST",

            data: serializedObject,

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