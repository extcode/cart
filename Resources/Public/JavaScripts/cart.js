function updateCountry(billingCountry, shippingCountry) {
    var postParams = {
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
            $("#shipping-method").html($(data).filter("#shipping-method").html());
            $("#payment-method").html($(data).filter("#payment-method").html());
            $("#checkout-review-table").html($(data).filter("#checkout-review-table").html());
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
            $("#shipping-method").html($(data).filter("#shipping-method").html());
            $("#payment-method").html($(data).filter("#payment-method").html());
            $("#coupons").html($(data).filter("#coupons").html());
            $("#checkout-review-table").html($(data).filter("#checkout-review-table").html());
        }
    });
}

$("#billingAddress\\:country").change(function () {
    var billingCountry = $(this).val();
    var shippingCountry = "";

    if(!$("#shipping_same_as_billing").is(":checked")) {
        shippingCountry = $("#shippingAddress\\:country").val();
    }

    updateCountry(billingCountry, shippingCountry);
});

$("#shippingAddress\\:country").change(function () {
    var billingCountry = $("#billingAddress\\:country").val();
    var shippingCountry = $(this).val();

    updateCountry(billingCountry, shippingCountry);
});

$("#shipping_same_as_billing").change(function() {
    $("#shipping-address").toggle(!this.checked);

    var billingCountry = $("#billingAddress\\:country").val();
    var shippingCountry = "";

    if(!$("#shipping_same_as_billing").is(":checked")) {
        shippingCountry = $("#shippingAddress\\:country").val();
        $("#shipping-address input").each(function() {
            if($(this).data("required")) {
                $(this).attr("required","required");
            }
        });
    } else {
        $("#shipping-address input").each(function() {
            $(this).removeAttr("required");
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

$("#payment-method").on("click", ".setPayment", function(e) {
    var url = $(this).attr("href");

    $.get( url, function( data ) {
        $("#shipping-method").html($(data).filter("#shipping-method").html());
        $("#payment-method").html($(data).filter("#payment-method").html());
        $("#checkout-review-table").html($(data).filter("#checkout-review-table").html());
    });

    e.preventDefault();
});

$("#shipping-method").on("click", ".setShipping", function(e) {
    var url = $(this).attr("href");

    $.get( url, function( data ) {
        $("#shipping-method").html($(data).filter("#shipping-method").html());
        $("#payment-method").html($(data).filter("#payment-method").html());
        $("#checkout-review-table").html($(data).filter("#checkout-review-table").html());
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

$("#add-product-form").submit(function(e) {

    var $form = $(this);
    var serializedObject = $form.serializeObject();

    if ($form.data("remote")) {
        $.ajax({
            async: "true",
            url: $form.attr("action"),
            type: "POST",

            data: serializedObject,

            success: function(data)
            {
                var response = JSON.parse(data);
                var message = "";
                if (response.status === "200") {
                    $("#tx-cart-minicart span.count").html(response.count);
                    $("#tx-cart-minicart span.net").html(response.net);
                    $("#tx-cart-minicart span.gross").html(response.gross);

                    if (response.count > 0) {
                        $("#link-to-checkout").show();
                    } else {
                        $("#link-to-checkout").hide();
                    }

                    $(document).trigger("status.cartWasChanged",[true]);

                    $form.each(function(){
                        this.reset();
                    });

                    message = $(".cart_form .form-success").html();
                    $(message).appendTo(".cart_form .form-message").delay(2000).fadeOut("slow", function() { $(this).remove(); });
                } else {
                    message = $(".cart_form .form-success").html();
                    $(message).appendTo(".cart_form .form-message").delay(2000).fadeOut("slow", function() { $(this).remove(); });
                }
            }
        });

        e.preventDefault();
    }
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