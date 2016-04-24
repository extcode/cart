$('#shipping_same_as_billing').change(function() {
    $('#shipping-address').toggle(!this.checked);
});