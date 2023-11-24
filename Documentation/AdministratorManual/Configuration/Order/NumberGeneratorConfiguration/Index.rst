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

The following options are available

.. container:: table-row

   Property
      options:prefix
   Data type
      string
   Description
      The prefix is put in front of the generated number.

.. container:: table-row

   Property
      opions:suffix
   Data type
      string
   Description
      The suffix is appended to the generated number.

.. container:: table-row

   Property
      opions:offset
   Data type
      integer
   Description
      The offset is always added to the counter in the database.

.. container:: table-row

   Property
      opions:format
   Data type
      string
   Description
      The format can be used to add leading zeros to the generated number.


An examples make the usage clear.
The configuration via the options is the same for all numbers using the
`generateNumber()` method from the `\Extcode\Cart\EventListener\Order\Create\Number`.
The new counter for the order number should be 23:

.. code-block:: typoscript

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

The generated order number would look like this: DEMO-000010023-SHOP.


