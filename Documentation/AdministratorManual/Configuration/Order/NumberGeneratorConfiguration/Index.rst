.. include:: ../../../../Includes.txt
.. _number-generator-configuration:

==============================
Number Generator Configuration
==============================

Order, invoice and delivery note numbers are created by EventListeners and
stored at the order. These can be configured and thus customized via options
when registering the EventListeners.
The options offer the possibility to add a prefix or suffix to the numbers,
to give an offset to let the first number start at e.g. 10001. Furthermore
you can define via the format how many leading zeros should be used in the
order number.
As in extcode/cart 7.x, the respective counter is stored and updated in
the `sys_registry`-table.

Usage example
=============
The configuration via the options is the same for all numbers using the
`generateNumber()` method from the `\Extcode\Cart\EventListener\Order\Create\Number`.


.. code-block:: yaml
   :caption: EXT:sitepackage/Configuration/Services.yaml

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
=================

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

