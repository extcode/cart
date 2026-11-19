.. include:: ../../Includes.rst.txt

==================================================================
Deprecation: #836 - Payment and shipping status in the TCA items
==================================================================

See `Issue 836 <https://github.com/extcode/cart/issues/836>`__

Description
===========

Until cart 12.3 own payment and shipping status were added to the items of
the status field in the TCA, e.g. by a TCA override:

.. code-block:: php
   :caption: EXT:my_extension/Configuration/TCA/Overrides/tx_cart_domain_model_order_payment.php

   $GLOBALS['TCA']['tx_cart_domain_model_order_payment']['columns']['status']['config']['items'][] = [
       'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:payment.status.refunded',
       'value' => 'refunded',
   ];

Since cart 12.4 the status are registered in
:file:`Configuration/OrderStatus.php`, see :ref:`developer-order-status`. The
items of the status fields of ``tx_cart_domain_model_order_payment`` and
``tx_cart_domain_model_order_shipping`` in the TCA of cart are empty.

Adding status to these items is deprecated and will stop working in cart
13.0.

Impact
======

Status in the items of the TCA are still used in the backend module, the order
details in the frontend and the TCA. They are added after the registered
status and a deprecation is logged.

Removing or reordering the status of cart by changing these items is not
supported anymore.

Affected Installations
======================

All installations with extensions which add status to the items of
``$GLOBALS['TCA']['tx_cart_domain_model_order_payment']['columns']['status']['config']['items']``
or
``$GLOBALS['TCA']['tx_cart_domain_model_order_shipping']['columns']['status']['config']['items']``.

Migration
=========

Remove the status from the TCA override and register them in
:file:`Configuration/OrderStatus.php` of your extension:

.. code-block:: php
   :caption: EXT:my_extension/Configuration/OrderStatus.php

   <?php

   use Extcode\Cart\OrderStatus\PaymentStatus;

   return [
       'payment' => [
           'refunded' => [
               'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:payment.status.refunded',
               'after' => [PaymentStatus::PAID],
               'before' => [PaymentStatus::CANCELED],
           ],
       ],
   ];

.. index:: Backend, TCA
