.. include:: ../../Includes.rst.txt

=========================================================
Breaking: #843 - Constructors of the attachment listeners
=========================================================

See `Issue 843 <https://github.com/extcode/cart/issues/843>`__

Description
===========

The listeners of the ``\Extcode\Cart\Event\Mail\AttachmentEvent``

*  ``\Extcode\Cart\EventListener\Mail\AttachmentFromTypoScript`` and
*  ``\Extcode\Cart\EventListener\Mail\AttachmentFromOrderItem``

read the configuration with the new
``\Extcode\Cart\Mail\PluginConfigurationProvider`` instead of the
``\TYPO3\CMS\Extbase\Configuration\ConfigurationManager``. Their constructors
take the ``PluginConfigurationProvider`` now, and the configuration is read when
the event is dispatched instead of in the constructor.

The ``PluginConfigurationProvider`` reads ``plugin.tx_cart`` of the page in the
backend, so the attachments of ``plugin.tx_cart.mail.<buyer|seller>`` are
added to the emails on status changes in the backend module too. Without
request, e.g. on the command line, the listeners add no attachments instead of
failing.

Impact
======

Code which creates one of the listeners with a ``ConfigurationManager`` fails.
The listeners created by the dependency injection are not affected.

Affected Installations
======================

All installations with extensions which create or extend one of the
listeners, e.g. to change the attachments.

Migration
=========

Pass the ``PluginConfigurationProvider`` to the constructor, or let the
dependency injection create the listener. To add own attachments, register an
own listener of the ``AttachmentEvent``.

.. index:: PHP-API
