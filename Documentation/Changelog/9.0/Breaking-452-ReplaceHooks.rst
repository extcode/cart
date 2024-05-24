.. include:: ../../Includes.txt

==============================
Breaking: #452 - Replace Hooks
==============================

See :issue:`452`

Description
===========

Existing Hooks have been removed. They were replaced by EventListeners.
The extension offered following hooks:

* `showCartActionAfterCartWasLoaded` in `\Extcode\Cart\Controller\Cart\CartController`
* `AddToCartFinisher` in `\Extcode\Cart\Domain\Finisher\Form\AddToCartFinisher`
* `MailAttachmentHook` in `\Extcode\Cart\Service\MailHandler`

Affected Installations
======================

All installations that used the hooks to programmatically adjust the behavior
of the extension are affected.

Migration
=========

* `showCartActionAfterCartWasLoaded` in `\Extcode\Cart\Controller\Cart\CartController`
  now needs to listen to the event `\Extcode\Cart\Event\Cart\BeforeShowCartEvent`
* `AddToCartFinisher` in `\Extcode\Cart\Domain\Finisher\Form\AddToCartFinisher`
  now needs to listen to the event `\Extcode\Cart\Event\Form\AddToCartFinisherEvent`
* `MailAttachmentHook` in `\Extcode\Cart\Service\MailHandler`
  now needs to listen to the event `\Extcode\Cart\Event\Mail\AttachmentEvent`

.. index:: API
