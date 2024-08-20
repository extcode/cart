.. include:: ../../../Includes.rst.txt

.. _number-generator-configuration:

================
Number Generator
================

In TypoScript is defined which numbers get generated while the configuration
of its format is done in `Services.yaml`.

Define which numbers are generated
==================================

.. code-block:: typoscript
   :caption: EXT:cart/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       settings {
           autoGenerateNumbers = order
       }
   }

plugin.tx_cart.settings
-----------------------

.. confval:: autoGenerateNumbers

   :Type: comma separated string
   :Default: order

   During the order process, the `\Extcode\Cart\Event\Order\NumberGeneratorEvent`
   is triggered. Generally, the order number is to be generated here.
   By adding further values like `invoice` and/or `delivery` these numbers
   will also be generated and saved directly after the order.

   Further values are also allowed. For this case then own EventListener must
   be registered.

   If the configuration is empty, all EventListener registered in
   extcode/cart on this event will be executed and appropriate numbers will
   be generated.


Format configuration
====================

Order, invoice and delivery note numbers are created by EventListeners and
stored at the order. These can be configured and thus customized via options
when registering the EventListeners.

The options offer the possibility to

* add a prefix to the number,
* add suffix to the number,
* give an offset to let the first number start at e.g. 10001.
* define how many leading zeros should be used in the order number.

The respective counter is stored and updated in the `sys_registry`-table.

The configuration via the options is the same for all numbers using the
`generateNumber()` method from the `\Extcode\Cart\EventListener\Order\Create\Number`.


.. code-block:: yaml
   :caption: Example configuration, can be set in e.g. EXT:sitepackage/Configuration/Services.yaml

   services:

     Extcode\Cart\EventListener\Order\Create\OrderNumber:
       arguments:
         $persistenceManager: '@TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager'
         $options:
           prefix: 'DEMO-'
           suffix: '-SHOP'
           offset: 10000
           format: "%09d"
       tags:
         - name: event.listener
           identifier: 'cart--order--create--order-number'
           event: Extcode\Cart\Event\Order\NumberGeneratorEvent
           after: 'cart--order--create--order'

Let's assume that the current counter for the order number is `23`.
The generated order number would look like this: `DEMO-000010023-SHOP`.

Available options
-----------------

.. confval:: options:prefix

   :Type: string

   The prefix is put in front of the generated number.

.. confval:: options:suffix

   :Type: string

   The suffix is appended to the generated number.

.. confval:: options:offset

   :Type: integer

   The offset is always added to the counter in the database.

.. confval:: options:format

   :Type: string

   The format can be used to add leading zeros to the generated number.

