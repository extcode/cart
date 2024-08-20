.. include:: ../../Includes.rst.txt

=========================================================
Breaking: #333 - Switch from jQuery to vanilla JavaScript
=========================================================

See :issue:`333`

Description
===========

Using the jQuery library is outdated. Therefore jQuery in the existing JavaScript code is replaced with vanilla
JavaScript. Furthermore is the JavaScript code splitted up and via
[Asset Viewhelper](https://docs.typo3.org/other/typo3/view-helper-reference/12.4/en-us/typo3/fluid/latest/Asset/Script.html) included in the templates and partials where the corresponding JavaScript is needed.

For better development of the JavaScript a build process is added, using [esbuild](https://esbuild.github.io/). The original JavaScript files are now located in `Build/Sources/` where you can find further information how to work with these files during development.

Affected Installations
======================

Installations which use the default templates and partials from the extension just do no longer need to include jQuery.
Installations which have individualized templates or partials might need to adapt templates or partials.

Migration
=========

**1. Remove old JavaScript**
It is no longer necessary to include jQuery for this extension.

The old JavaScript file `/Resources/Public/JavaScripts/cart.js` does no longer exist. If it was included in some
JavaScript bundling then it must be removed.

**2. Migrate to new JavaScript**
If you have individual templates and/or partials than you can migrate in two ways:

**2. ALTERNATIVE A: Add JavaScript via TypoScript**
The easier but less performant migration is to include all JavaScript files with TypoScript, e.g. as follows:

.. code-block:: typoscript
    page.includeJSFooter {
        tx_cart_add_to_cart = EXT:cart/Resources/Public/JavaScript/add_to_cart.js
        tx_cart_change_be_variant = EXT:cart/Resources/Public/JavaScript/change_be_variant.js
        tx_cart_prevent_multi_submit = EXT:cart/Resources/Public/JavaScript/prevent_multi_submit.js
        tx_cart_update_country = EXT:cart/Resources/Public/JavaScript/update_country.js
        tx_cart_update_currency = EXT:cart/Resources/Public/JavaScript/update_currency.js
        tx_cart_update_payment_shipping = EXT:cart/Resources/Public/JavaScript/update_payment_shipping.js
    }

.. note::
  The name of the directory for the JavaScript files is no longer `/JavaScripts` (plural) but `/JavaScript` (singular).

**2. ALTERNATIVE B: Add JavaScript via Asset Viewhelper**
The more performant way requires more job some job in your partials and templates. Even templates of extensions like e.g.
`EXT:cart_products` are affected.

It's necessary to include the [asset viewhelpers](https://docs.typo3.org/other/typo3/view-helper-reference/12.4/en-us/typo3/fluid/latest/Asset/Script.html)
as they are included in the updated partials and templates.

The following partials are affected:

* `Resources/Private/Partials/Cart/CouponForm.html` → includes `EXT:cart/Resources/Public/JavaScript/prevent_multi_submit.js`
* `Resources/Private/Partials/Cart/CurrencyForm.html` → includes `EXT:cart/Resources/Public/JavaScript/update_currency.js`
* `Resources/Private/Partials/Cart/OrderForm.html` → includes `EXT:cart/Resources/Public/JavaScript/prevent_multi_submit.js`
* `Resources/Private/Partials/Cart/OrderForm/Address/Shipping.html` → includes `EXT:cart/Resources/Public/JavaScript/update_country.js`
* `Resources/Private/Partials/Cart/OrderForm/PaymentMethod.html` → includes `EXT:cart/Resources/Public/JavaScript/update_payment_shipping.js`
* `Resources/Private/Partials/Cart/OrderForm/ShippingMethod.html` → includes `EXT:cart/Resources/Public/JavaScript/update_payment_shipping.js`
* `Resources/Private/Partials/Cart/ProductForm.html` → includes `EXT:cart/Resources/Public/JavaScript/prevent_multi_submit.js`

The following templates are affected:

* `Resources/Private/Templates/Cart/ShowStep2.html` → includes `EXT:cart/Resources/Public/JavaScript/prevent_multi_submit.js` and `EXT:cart/Resources/Public/JavaScript/update_country.js`
* `Resources/Private/Templates/Cart/ShowStep3.html` → includes `EXT:cart/Resources/Public/JavaScript/prevent_multi_submit.js`
* `Resources/Private/Templates/Cart/ShowStep4.html` → includes `EXT:cart/Resources/Public/JavaScript/prevent_multi_submit.js`
* `Resources/Private/Templates/Currency/Edit.html` → includes `EXT:cart/Resources/Public/JavaScript/update_currency.js`


**3. Update HTML**

**3.a) Replace inline JavaScript with hidden div (in another file)**
The inline JavaScript in your overrides of `Resources/Private/Templates/Cart/Show.html` can be removed,
instead a hidden `div` has to be added in your overrides of `Resources/Private/Partials/Cart/OrderForm/Address/Shipping.html`

Remove the following `script` from your overrides of `Resources/Private/Templates/Cart/Show.html`
and from `Resources/Private/Templates/Cart/Cart/ShowStep2.html`

.. code-block:: html
    ## Delete the following in Resources/Private/Templates/Cart/Show.html and Resources/Private/Templates/Cart/Cart/ShowStep2.html
    <script type="text/javascript">
        var update_country = '<f:format.raw><f:uri.action controller="Cart\Country" action="update" pageType="2278001"/></f:format.raw>';
    </script>

Add the following `div` in your overrides of `Resources/Private/Partials/Cart/OrderForm/Address/Shipping.html`

.. code-block:: html
    ## in Resources/Private/Partials/Cart/OrderForm/Address/Shipping.html
    <div hidden data-ajax-update-country-action-url="{f:uri.action(controller: 'Cart\Country', action: 'update', pageType: '2278001') -> f:format.raw()}"></div>

**3.b) Move checkbox for shipping address to partial**
The checkbox in the order form where the customer can choose whether billing and shipping address differ moved into
a partial.
`Resources/Private/Partials/Cart/OrderForm/Address/Shipping.html

Delete the following from your overrides of `Resources/Private/Partials/Cart/OrderForm.html`
and `Resources/Private/Templates/Cart/Cart/ShowStep2.html`.

.. code-block:: html
    <div class="form-list shipping-same-as-billing-wrapper">
        <div class="control">
            <label for="shipping-same-as-billing">
                <f:form.checkbox property="shippingSameAsBilling"
                                 id="shipping-same-as-billing"
                                 value="1"
                                 title="{f:translate(key:'tx_cart.same_address')}"
                                 additionalAttributes="{f:if(condition:'{cart.shippingSameAsBilling}', then: '{checked: \'checked\'}')}"/>
                <span><f:translate key="tx_cart.same_address"/></span>
            </label>
        </div>
    </div>

Add in your override of `Resources/Private/Partials/Cart/OrderForm/ShippingMethod.html`

.. code-block:: html
    <div class="form-list shipping-same-as-billing-wrapper">
        <div class="control">
            <label for="shipping-same-as-billing">
                <f:form.checkbox property="shippingSameAsBilling"
                                 id="shipping-same-as-billing"
                                 value="1"
                                 title="{f:translate(key:'tx_cart.same_address')}"
                                 additionalAttributes="{f:if(condition:'{cart.shippingSameAsBilling}', then: '{checked: \'checked\'}')}"/>
                <span><f:translate key="tx_cart.same_address"/></span>
            </label>
        </div>
    </div>

**4. Adapt JS in templates of other extensions (only needed when you chose step 2 ALTERNATIVE B) above)**
When using `EXT:cart_products` you need also to adapt `/Resources/Private/Partials/Product/CartForm.html`:
You need to insert the following viewHelper:

.. code-block:: html
    <f:asset.script identifier="add-to-cart" src="EXT:cart/Resources/Public/JavaScript/add_to_cart.js" />
    <f:asset.script identifier="change-be-variant" src="EXT:cart/Resources/Public/JavaScript/change_be_variant.js" />

Other extension which use a similar `CartForm.html` needs the same adaption.

.. index:: Template, Frontend, JavaScript
