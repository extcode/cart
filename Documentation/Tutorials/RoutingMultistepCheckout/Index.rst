.. include:: ../../Includes.rst.txt

.. _routing-multistep-checkout:

==============================
Routing for multistep checkout
==============================

The multistep checkout need routeEnhancers for readable urls.

The following example show a multi language snippet.

.. note::
   Unfortunately it's not
   perfect as the final url `thank-you-for-your-order` is used even if the last
   step results in errors. In this case the url-segment `checkout` should be
   used but it's not. Otherwise it's working as expected.

.. code-block:: yaml
   :caption: config/sites/your-page/config.yaml

   routeEnhancers:
     Cart:
       type: Extbase
       limitToPages:
         - 123 // PID where the cart plugin is located
       extension: Cart
       plugin: Cart
       routes:
         - routePath: '/{localized_thanks}'
           _controller: 'Cart\Order::create'
         - routePath: '/{step}'
           _controller: 'Cart\Cart::show'
           _arguments:
             product-title: product
       defaultController: 'Cart\Cart::show'
       aspects:
         localized_thanks:
           type: LocaleModifier
           default: 'thank-you-for-your-order'
           localeMap:
             - locale: 'de_.*'
               value: 'danke-fuer-deine-bestellung'
         step:
           type: StaticValueMapper
           map:
             products: 1
             address: 2
             shipping-and-payment: 3
             checkout: 4
           localeMap:
             - locale: 'de_.*'
               map:
                 artikel: 1
                 adresse: 2
                 versand-zahlmethode: 3
                 pruefung: 4
