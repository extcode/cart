.. include:: ../../Includes.rst.txt

==========================================================
Feature: #763 - Event to generate documents in the backend
==========================================================

See `Pull request 763 <https://github.com/extcode/cart/pull/763>`__ and
`Issue 743 <https://github.com/extcode/cart/issues/743>`__

Description
===========

The ``DocumentController`` of the backend module no longer creates the PDF
documents with the ``PdfService`` of cart_pdf. It dispatches the new event
``\Extcode\Cart\Event\Document\GenerateDocumentEvent`` with the order item and
the document type (e.g. ``invoice``, ``delivery``) instead. The event is
stoppable, a listener can stop the propagation with
``setPropagationStopped(true)``.

Impact
======

Any extension can generate the documents of an order by registering an event
listener for ``GenerateDocumentEvent``. cart_pdf provides such a listener, it
needs a version that supports this event.

.. index:: Backend, PHP-API
