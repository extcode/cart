document.addEventListener('DOMContentLoaded', function() {
    function setValue(parentElement, targetElementClass, value) {
        let targetElement = parentElement.querySelector(targetElementClass);
        if (targetElement) {
            targetElement.querySelector('.price').innerHTML = value;
        }
    }

    let productPrice = document.querySelector('#product-price');

    document.querySelector('#be-variants-select').addEventListener('change', function(){
        const selectedOption = this.selectedOptions[0];
        const specialPrice = selectedOption.dataset['specialPrice'];
        const regularPrice = selectedOption.dataset['regularPrice'];
        const specialPricePercentageDiscount = selectedOption.dataset['specialPricePercentageDiscount'];

        setValue(productPrice, '.special_price', specialPrice);
        setValue(productPrice, '.regular_price', regularPrice);
        setValue(productPrice, '.special_price_percentage_discount', specialPricePercentageDiscount);
    });
});
