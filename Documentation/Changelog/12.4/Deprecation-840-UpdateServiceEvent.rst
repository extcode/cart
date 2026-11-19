.. include:: ../../Includes.rst.txt

=========================================
Deprecation: #840 - UpdateServiceEvent
=========================================

See `Issue 840 <https://github.com/extcode/cart/issues/840>`__

Description
===========

The event ``\Extcode\Cart\Event\Order\UpdateServiceEvent`` is replaced by the
``\Extcode\Cart\Event\Order\AfterStatusChangeEvent``, which is dispatched for
every status change of the ``StatusChangeService`` and contains the old and the
new status, the source, the initiator and the backend user.

The event listener ``\Extcode\Cart\EventListener\Order\Update\LogServiceUpdate``
of cart, which logged the old and the new status of the ``UpdateServiceEvent``,
is not registered anymore; the order history replaces it.

Both classes are deprecated and will be removed in cart 13.0.

Impact
======

The ``UpdateServiceEvent`` is still dispatched after a status change in the
backend. If an extension listens to it, a deprecation is logged.

Affected Installations
======================

All installations with extensions which listen to the ``UpdateServiceEvent``
or use the ``LogServiceUpdate``.

Migration
=========

Listen to the ``AfterStatusChangeEvent`` instead:

.. code-block:: php

   use Extcode\Cart\Event\Order\AfterStatusChangeEvent;
   use TYPO3\CMS\Core\Attribute\AsEventListener;

   #[AsEventListener('my-extension--order--after-status-change')]
   final readonly class MyListener
   {
       public function __invoke(AfterStatusChangeEvent $event): void
       {
           $service = $event->getService();
           $oldStatus = $event->getFromStatus();
           $newStatus = $event->getToStatus();
       }
   }

.. index:: Backend, PHP-API
