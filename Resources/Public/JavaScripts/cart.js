// global function
;(function (window, document, $, undefined) {
    'use strict';

    var W = $(window),
        U = typeof undefined,
        D = $(document),
        toTopEl = '#to-top',
        $navBar = $('.nav-bar');

    D.ready(function () {
        $('[data-add-to-cart="form"]').click(function (e) {
            e.preventDefault();
            $.get(
                $(this).attr('href'),
                function(data){
                    $('[data-add-to-cart="result"]').html(data);
                });
        });
    });

    D.ajaxComplete(function () {
        $('[data-add-to-cart-uri]').submit(function (e) {
            e.preventDefault();
            $('[data-add-to-cart-uri]').unbind( "submit" );

            var form = $(this);
            var url = form.attr('data-add-to-cart-uri');

            var data = new FormData( this );
            var submitButton = $("button[type='submit']", form);
            data.append($(submitButton).attr('name'), $(submitButton).attr('value'));

            $.ajax({
                type: "POST",
                url: url,
                data: data,
                processData: false,
                contentType: false,
                success: function(data)
                {
                    handleAddToCartSuccessResponse(form, data);
                }
            });

        });
    });

}(window, document, jQuery));

function updateCountry(billingCountry, shippingCountry) {
    var postParams = {
        "tx_cart_cart[shipping_same_as_billing]": $("#shipping-same-as-billing").is(":checked"),
        "tx_cart_cart[billing_country]": billingCountry,
        "tx_cart_cart[shipping_country]": shippingCountry
    };

    $.ajax({
        async: "true",
        url: update_country,
        type: "POST",

        data: postParams,

        success: function(data)
        {
            $("#form-cart").html($(data).filter("#form-cart").html());
            $("#checkout-step-shipping-method").html($(data).filter("#checkout-step-shipping-method").html());
            $("#checkout-step-payment-method").html($(data).filter("#checkout-step-payment-method").html());
            $("#checkout-step-summary").html($(data).filter("#checkout-step-summary").html());
        }
    });
}

function updateCurrency(currencyCode, action) {
    var postParams = {
        "tx_cart_cart[currencyCode]": currencyCode
    };

    $.ajax({
        async: "true",
        url: action,
        type: "POST",

        data: postParams,

        success: function(data)
        {
            $("#form-cart").html($(data).filter("#form-cart").html());
            $("#checkout-step-shipping-method").html($(data).filter("#checkout-step-shipping-method").html());
            $("#checkout-step-payment-method").html($(data).filter("#checkout-step-payment-method").html());
            $("#checkout-step-coupon").html($(data).filter("#checkout-step-coupon").html());
            $("#checkout-step-summary").html($(data).filter("#checkout-step-summary").html());
        }
    });
}

$("#billingAddress-country").change(function () {
    var billingCountry = $(this).val();
    var shippingCountry = "";

    if(!$("#shipping-same-as-billing").is(":checked")) {
        shippingCountry = $("#shippingAddress-country").val();
    }

    updateCountry(billingCountry, shippingCountry);
});

$("#shippingAddress-country").change(function () {
    var billingCountry = $("#billingAddress-country").val();
    var shippingCountry = $(this).val();

    updateCountry(billingCountry, shippingCountry);
});

$("#shipping-same-as-billing").change(function() {
    $("#checkout-step-shipping-address").toggle(!this.checked);

    var billingCountry = $("#billingAddress-country").val();
    var shippingCountry = $("#shippingAddress-country").val();

    if(!$("#shipping-same-as-billing").is(":checked")) {
        $("#checkout-step-shipping-address input, #checkout-step-shipping-address select").each(function() {
            if($(this).data("disable-shipping")) {
                $(this).prop("disabled", false);
            }
        });
    } else {
        $("#checkout-step-shipping-address input, #checkout-step-shipping-address select").each(function() {
            if($(this).data("disable-shipping")) {
                $(this).prop("disabled", true);
            }
        });
    }

    updateCountry(billingCountry, shippingCountry);
});

$(".cart-currency-selector").change(function () {
    updateCurrency($(this).val(), $(this).closest("form").attr("action"));
});

$(".currency-selector").change(function () {

    var postParams = {
        "tx_cart_currency[currencyCode]": $(this).val()
    };

    $.ajax({
        async: "true",
        url: $(this).closest("form").attr("action"),
        type: "POST",

        data: postParams,

        success: function(data)
        {
            location.reload();
        }
    });
});

$("#checkout-step-payment-method").on("click", ".set-payment", function(e) {
    var url = $(this).attr("href");

    $.get( url, function( data ) {
        $("#checkout-step-shipping-method").html($(data).filter("#checkout-step-shipping-method").html());
        $("#checkout-step-payment-method").html($(data).filter("#checkout-step-payment-method").html());
        $("#checkout-step-summary").html($(data).filter("#checkout-step-summary").html());
    });

    e.preventDefault();
});

$("#checkout-step-shipping-method").on("click", ".set-shipping", function(e) {
    var url = $(this).attr("href");

    $.get( url, function( data ) {
        $("#checkout-step-shipping-method").html($(data).filter("#checkout-step-shipping-method").html());
        $("#checkout-step-payment-method").html($(data).filter("#checkout-step-payment-method").html());
        $("#checkout-step-summary").html($(data).filter("#checkout-step-summary").html());
    });

    e.preventDefault();
});

$("#be-variants-select").change(function () {
    var special_price = $(this).children().filter(":selected").data("special-price");
    var regular_price = $(this).children().filter(":selected").data("regular-price");
    var special_price_percentage_discount = $(this).children().filter(":selected").data("special-price-percentage-discount");

    $("#product-price .special_price .price").html(special_price);
    $("#product-price .regular_price .price").html(regular_price);
    $("#product-price .special_price_percentage_discount .price").html(special_price_percentage_discount);
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
            o[this.name].push(this.value || "");
        } else {
            o[this.name] = this.value || "";
        }
    });
    return o;
};

function handleAddToCartSuccessResponse(form, data) {
    var messageBlock;
    var messageTimeout = form.find('[data-ajax-message-timeout]').data('ajax-message-timeout');

    if (!messageTimeout) {
        messageTimeout = 3000;
    }

    var response = JSON.parse(data);
    if (response.status === "200") {
        $("#cart-preview .cart-preview-count").html(response.count);
        $("#cart-preview .net").html(response.net);
        $("#cart-preview .gross").html(response.gross);

        if (response.count > 0) {
            $("#cart-preview .checkout-link").show();
        }else {
            $("#cart-preview .checkout-link").hide();
        }

        $(document).trigger("status.cartWasChanged", [true]);

        form.each(function () {
            this.reset();
        });

        form.find('[data-ajax-success-message]').html(response.messageBody);
        form.find('[data-ajax-success-block]').show().delay(messageTimeout).fadeOut("slow");
    } else {
        form.find('[data-ajax-error-message]').html(response.messageBody);
        form.find('[data-ajax-error-block]').show().delay(messageTimeout).fadeOut("slow");
    }
}

$("[data-ajax='1']").submit(function(e) {
    var form = $(this);
    var serializedObject = form.serializeObject();

    $.ajax({
        async: "true",
        url: form.attr("action"),
        type: "POST",

        data: serializedObject,

        success: function(data)
        {
            handleAddToCartSuccessResponse(form, data);
        }
    });

    e.preventDefault();
});

$("#form-cart").submit(function() {
    $("input:submit").attr("disabled", true);
});
$("#form-order").submit(function() {
    $("input:submit").attr("disabled", true);
});
$("#form-coupon").submit(function() {
    $("input:submit").attr("disabled", true);
});
