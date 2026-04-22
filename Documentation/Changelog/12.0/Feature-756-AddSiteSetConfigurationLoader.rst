.. include:: ../../Includes.rst.txt

=================================================
Feature: #756 - Add Site Set Configuration Loader
=================================================

See `Issue 756 <https://github.com/extcode/cart/issues/756>`__

Description
===========

If you want to switch to the Site Sets configuration loader,
you have to alias the new classes.

You can decide to move some configuration to Site Sets and
leave others in TypoScript

.. code-block:: php

    $services
        ->alias(
            \Extcode\Cart\Configuration\Loader\PaymentMethodsLoaderInterface::class,
            \Extcode\Cart\Configuration\Loader\SiteSets\PaymentMethodsLoader::class
        )
    ;

.. code-block:: yaml

    services:
        Extcode\Cart\Configuration\Loader\PaymentMethodsLoaderInterface:
             alias: Extcode\Cart\Configuration\Loader\SiteSets\PaymentMethodsLoader

.. NOTE::
   Not all configurations can be moved to Site Sets with the 12.0.0 release.
   Others will follow in the near future with a minor release.

Impact
======

No direct impact.

