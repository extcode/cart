.. include:: ../../Includes.txt

Events
======

=============================================== ========================================================================
Event Class Name                                Description
=============================================== ========================================================================
Extcode\Cart\Event\ProcessOrderCheckStockEvent  The event is triggered in the
                                                `\Extcode\Cart\Controller\Cart\OrderController::createAction`
                                                and allows to cancel the further ordering process if the number of
                                                products in the shopping cart is no longer available in the meantime.
Extcode\Cart\Event\ProcessOrderCreateEvent      The event is triggered in the
                                                `\Extcode\Cart\Controller\Cart\OrderController::createAction`
                                                and allows the order process to be executed, intercepted from saving
                                                the order to generating the order number and sending the email
                                                notifications.
=============================================== ========================================================================
