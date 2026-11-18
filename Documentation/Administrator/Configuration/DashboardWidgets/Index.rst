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
*  **Orders Today**, **Orders this Month**: number of orders today or in
   the current month, without canceled payments
*  **Turnover Today**, **Turnover this Month**: gross total of the orders
   today or in the current month, without canceled payments

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
*  `extcode.cart.provider.orders_today` and
   `extcode.cart.provider.orders_this_month`
   (`\Extcode\Cart\Widgets\Provider\OrdersInPeriodProvider`)
*  `extcode.cart.provider.turnover_today` and
   `extcode.cart.provider.turnover_this_month`
   (`\Extcode\Cart\Widgets\Provider\TurnoverInPeriodProvider`)

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

   Several status are given as list or comma separated string; an order
   matches if it has one of them, e.g. only open and paid invoices:
   `['payment' => ['status' => ['open', 'paid']]]` or
   `['payment' => ['status' => 'open,paid']]`.

   The status of a payment are `open`, `pending`, `paid` and `canceled`. The
   number widgets show orders with the payment status `open`, `pending` or
   `paid` by default, so canceled orders are left out.

.. confval:: storagePids

   :Type: array or comma separated string of page uids
   :Default: `[]`

   Storage folders of the orders. All storage folders are taken into account
   if empty. In both cases only storage folders the backend user may show are
   used.

Fields of the following options are fields of the order
(`tx_cart_domain_model_order_item`), with or without the table name. Other
fields throw an exception.

Options of the chart and number providers
-----------------------------------------

These options apply to `OrdersPerDayProvider`, `TurnoverPerDayProvider`,
`OrdersInPeriodProvider` and `TurnoverInPeriodProvider`.

.. confval:: fieldName

   :Type: string
   :Default: `order_date`

   Date field of the order which assigns the order to a day or period.

Options of OrdersPerDayProvider and TurnoverPerDayProvider
----------------------------------------------------------

.. confval:: days

   :Type: int
   :Default: 28

   Number of days before today shown in the chart.

Options of OrdersInPeriodProvider and TurnoverInPeriodProvider
--------------------------------------------------------------

.. confval:: period

   :Type: string
   :Default: `today`

   Period up to the end of today: `today`, `week` (since Monday), `month` or
   `year`. Other values throw an exception. Days, weeks, months and years
   start in the time zone of the server.

Options of TurnoverPerDayProvider and TurnoverInPeriodProvider
--------------------------------------------------------------

.. confval:: sum

   :Type: string
   :Default: `total_gross`

   Field of the order which is summed up, e.g. `total_net`.

The turnover widgets add up the amounts of all orders regardless of their
currency. Restrict them with `storagePids` if a site uses its own storage
folder for orders in another currency.

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

Number widgets
==============

The number widgets use the widget `\Extcode\Cart\Widgets\NumberWidget`. Like
the `NumberWithIconWidget` of the dashboard it shows a number with an icon
(option `icon`), but the options `title` and `subtitle` are optional, so the
title of the widget is not repeated. With a data provider implementing
`\Extcode\Cart\Widgets\AmountDataProviderInterface`, like the
`TurnoverInPeriodProvider`, the number is shown as amount with currency. The
option `currency` takes the arguments of the currency ViewHelper
(`f:format.currency`), the default is `1.234,56 €`.

The following example adds a widget "Turnover this Week" in US dollars:

.. code-block:: php
   :caption: EXT:site_package/Configuration/Services.php

   <?php

   declare(strict_types=1);

   use Extcode\Cart\Widgets\NumberWidget;
   use Extcode\Cart\Widgets\Provider\TurnoverInPeriodProvider;
   use Symfony\Component\DependencyInjection\ContainerBuilder;
   use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
   use Symfony\Component\DependencyInjection\Reference;
   use TYPO3\CMS\Backend\View\BackendViewFactory;
   use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;

   return static function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder): void {
       if (!$containerBuilder->hasDefinition(BarChartWidget::class)) {
           return;
       }

       $services = $configurator->services();

       $services->set('site_package.provider.turnover_this_week')
           ->class(TurnoverInPeriodProvider::class)
           ->autowire()
           ->arg('$options', [
               'period' => 'week',
           ]);

       $services->set('dashboard.widget.site_package.turnover_this_week')
           ->class(NumberWidget::class)
           ->arg('$backendViewFactory', new Reference(BackendViewFactory::class))
           ->arg('$dataProvider', new Reference('site_package.provider.turnover_this_week'))
           ->arg('$options', [
               'icon' => 'actions-money',
               'currency' => [
                   'currencySign' => '$',
                   'decimalSeparator' => '.',
                   'thousandsSeparator' => ',',
                   'prependCurrency' => true,
                   'separateCurrency' => false,
               ],
           ])
           ->tag('dashboard.widget', [
               'identifier' => 'TurnoverThisWeek',
               'groupNames' => 'cart',
               'title' => 'Turnover this Week',
               'description' => 'Gross total of all orders in the current week',
               'iconIdentifier' => 'content-widget-number',
           ]);
   };
