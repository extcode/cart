.. include:: ../../../Includes.rst.txt

.. _dashboard_widgets:

=================
Dashboard widgets
=================

If the dashboard (`typo3/cms-dashboard`) is installed, the extension adds the
following widgets to the widget group "Cart":

*  **Orders per Day**: number of paid but not shipped orders per day
*  **Turnover per Day**: gross total of all orders per day
*  **Latest Orders (paid, not shipped)**: the ten latest paid but not shipped
   orders with a link to the order in the backend module

The widgets only show orders in storage folders the backend user may show
(page permissions and mount points), admins see all orders.

Configuration of the data providers
===================================

The data of the widgets comes from data providers, which are configured in
the service configuration of the extension:

*  `extcode.cart.provider.orders_per_day`
   (`\Extcode\Cart\Widgets\Provider\OrdersPerDayProvider`)
*  `extcode.cart.provider.turnover_per_day`
   (`\Extcode\Cart\Widgets\Provider\TurnoverPerDayProvider`)
*  `extcode.cart.provider.payment_paid_shipping_open`
   (`\Extcode\Cart\Widgets\Provider\OrderItemsProvider`)

To change the options, overwrite the service in the `Configuration/Services.php`
of your site package. The site package has to depend on `extcode/cart`, so its
service configuration is loaded afterwards.

.. code-block:: php
   :caption: EXT:site_package/Configuration/Services.php

   <?php

   declare(strict_types=1);

   use Extcode\Cart\Widgets\Provider\TurnoverPerDayProvider;
   use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

   return static function (ContainerConfigurator $configurator): void {
       $configurator->services()
           ->set('extcode.cart.provider.turnover_per_day')
           ->class(TurnoverPerDayProvider::class)
           ->autowire()
           ->arg('$options', [
               'days' => 14,
               'storagePids' => [105],
               'filter' => [
                   'payment' => [
                       'status' => 'paid',
                   ],
               ],
           ]);
   };

The same way you can register further widgets with own identifiers, e.g. a
second turnover widget for another storage folder.

Options of all providers
------------------------

.. confval:: filter

   :Type: array
   :Default: `[]`

   Status the payment and/or the shipping of an order must have, e.g.
   `['payment' => ['status' => 'paid'], 'shipping' => ['status' => 'open']]`.
   All orders are taken into account if empty.

.. confval:: storagePids

   :Type: array or comma separated string of page uids
   :Default: `[]`

   Storage folders of the orders. All storage folders are taken into account
   if empty. In both cases only storage folders the backend user may show are
   used.

Fields of the following options are fields of the order
(`tx_cart_domain_model_order_item`), with or without the table name. Other
fields throw an exception.

Options of OrdersPerDayProvider and TurnoverPerDayProvider
----------------------------------------------------------

.. confval:: days

   :Type: int
   :Default: 28

   Number of days before today shown in the chart.

.. confval:: fieldName

   :Type: string
   :Default: `order_date`

   Date field of the order which assigns the order to a day.

Options of TurnoverPerDayProvider
---------------------------------

.. confval:: sum

   :Type: string
   :Default: `total_gross`

   Field of the order which is summed up, e.g. `total_net`.

Options of OrderItemsProvider
-----------------------------

.. confval:: order_by

   :Type: string
   :Default: `crdate`

   Field to sort the orders by.

.. confval:: order_order

   :Type: string
   :Default: `desc`

   Sort direction, `asc` or `desc`.

.. confval:: limit

   :Type: int
   :Default: 10

   Number of orders in the list.
