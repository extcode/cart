.. include:: ../../Includes.rst.txt

======================================================
Feature: #876 - Stored status of a stale status change
======================================================

See `Issue 876 <https://github.com/extcode/cart/issues/876>`__

Description
======================================================

If the payment or the shipping of an order has not the expected status
anymore, ``\Extcode\Cart\OrderStatus\StatusChangeService::change()`` returns
the outcome ``Stale``. The ``\Extcode\Cart\OrderStatus\StatusChangeResult``
now contains the status stored in the database in ``storedStatus``. For all
other outcomes it is ``null``.

See :ref:`developer-order-status`.

Impact
======

Extensions which try the change again with the stored status, e.g. for
payment providers whose webhook and the return of the customer arrive at the
same time, no longer read the status from the database themselves.

.. index:: PHP-API
