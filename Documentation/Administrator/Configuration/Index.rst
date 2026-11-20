.. include:: ../../Includes.rst.txt

=============
Configuration
=============

The following pages contain needed information for the configuration of the
extension with TypoScript, PageTS and YAML.

.. toctree::
   :maxdepth: 5
   :titlesonly:

   Base/Index
   Cart/Index
   Currency/Index
   Email/Index
   NumberGenerator/Index
   PaymentMethods/Index
   ShippingMethods/Index
   TaxClasses/Index
   BackendModule/Index
   DashboardWidgets/Index
   Ajax/Index

.. _configuration-site-sets:

Site sets
=========

The payment methods, the shipping methods, the tax classes and the email
addresses can also be configured with site sets and site settings instead of
TypoScript. Site settings are typed and can be edited in the backend module
"Site Settings".

============================  =============================  =====================================================================
Site set                      Site settings                  Loader interface
============================  =============================  =====================================================================
``extcode/cart-payments``     ``extcode.cart.payments``      ``\Extcode\Cart\Configuration\Loader\PaymentMethodsLoaderInterface``
``extcode/cart-shippings``    ``extcode.cart.shippings``     ``\Extcode\Cart\Configuration\Loader\ShippingMethodsLoaderInterface``
``extcode/cart-taxclasses``   ``extcode.cart.taxClasses``    ``\Extcode\Cart\Configuration\Loader\TaxClassLoaderInterface``
``extcode/cart-mail``         ``extcode.cart.mail``          ``\Extcode\Cart\Configuration\Loader\MailConfigurationLoaderInterface``
============================  =============================  =====================================================================

The site settings use the namespace ``extcode.cart`` with the keys of the
TypoScript, e.g. ``plugin.tx_cart.taxClasses`` becomes
``extcode.cart.taxClasses``. The special options (``plugin.tx_cart.specials``)
can only be configured with TypoScript.

To use a site set:

#. Add the site set to the dependencies of the site:

   .. code-block:: yaml
      :caption: config/sites/<site>/config.yaml

      dependencies:
        - extcode/cart-payments
        - extcode/cart-shippings
        - extcode/cart-taxclasses

#. Adapt the site settings in the backend module "Site Settings" or in
   :file:`config/sites/<site>/settings.yaml`.

#. The payment methods, the shipping methods and the tax classes are read from
   TypoScript by default. Use the site sets loaders with an alias of the loader
   interface, each configuration can be moved separately:

   .. code-block:: yaml
      :caption: EXT:sitepackage/Configuration/Services.yaml

      services:
        Extcode\Cart\Configuration\Loader\PaymentMethodsLoaderInterface:
          alias: Extcode\Cart\Configuration\Loader\SiteSets\PaymentMethodsLoader
          public: true
        Extcode\Cart\Configuration\Loader\ShippingMethodsLoaderInterface:
          alias: Extcode\Cart\Configuration\Loader\SiteSets\ShippingMethodsLoader
          public: true
        Extcode\Cart\Configuration\Loader\TaxClassLoaderInterface:
          alias: Extcode\Cart\Configuration\Loader\SiteSets\TaxClassLoader
          public: true

   The email addresses of the site set ``extcode/cart-mail`` are always read
   from the site settings. They are used with the feature toggle
   ``cart.useNewOrderMailSender``, see :ref:`mail_addresses`.

Differences to TypoScript:

*  The values are typed, e.g. ``extra: 7.95`` is a number and the email
   addresses are lists instead of comma separated strings.
*  The TypoScript copy syntax ``at < .de`` does not exist in YAML. Use an anchor
   and aliases to use the configuration of a country for other countries:

   .. code-block:: yaml
      :caption: config/sites/<site>/settings.yaml

      extcode:
        cart:
          payments:
            countries:
              de: &de
                preset: 1
                options:
                  1:
                    title: 'Prepayment'
                    extra: 0.0
                    taxClassId: 1
                    status: 'open'
              at: *de
              ch: *de

*  The payment methods, the shipping methods and the tax classes are read from
   the site of the current request, the email addresses from the site of the
   storage page of the order.
