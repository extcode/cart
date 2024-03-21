.. include:: ../../Includes.txt

=============================
Breaking: #452 - Remove Hooks
=============================

See :issue:`452`

Description
===========

Existing Hooks have been removed. They were replaced by EventListeners.
The extension offered following hooks:

* `showCartActionAfterCartWasLoaded` in `\Extcode\Cart\Controller\Cart\CartController`

Affected Installations
======================

All installations that used the hooks to programmatically adjust the behavior
of the extension are affected.

Migration
=========

* `showCartActionAfterCartWasLoaded` in `\Extcode\Cart\Controller\Cart\CartController`
  now needs to listen to the event `\Extcode\Cart\Event\Cart\BeforeShowCartEvent`

.. index:: API
