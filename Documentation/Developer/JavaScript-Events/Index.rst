.. include:: ../../Includes.rst.txt

=================
JavaScript Events
=================

Multiple `custom JavaScript events <https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent>`__
allow to react with own JavaScript on JavaScript which is executed after certain
user actions.


List of events
==============
The following events exists:

=========================================== ====================================
Event name                                  User action which triggers the event
=========================================== ====================================
`extcode:be-variant-was-changed`            A BE variant of a product was chosen.
`extcode:country-updated`                   The country of billing or shipping address is updated.
`extcode:currency-updated`                  The currency of the cart is updated.
`extcode:hide-message-block`                The success/error message after adding a product to the cart will be hidden.
`extcode:minicart-was-updated`              A product is added to the cart (which updates the mini cart).
`extcode:render-add-to-cart-result-message` A product is added to the cart which renders a success (or error) message.
`extcode:set-payment`                       Another payment option is chosen.
`extcode:set-shipping`                      Another shipping option is chosen.
=========================================== ====================================

.. TIP::
   The best way to understand when the event is executed and which data it
   contains can be seen in it's context. Search in the directory
   `/Build/Sources/` for the event name.

How to listen for events
========================

Every event comes with a detail object which contains data relevant for this
specific event.

The following example shows how to listen for an event.

.. code-block:: javascript
   :caption: Extract of some JavaScript added with e.g. an AssetViewhelper
   
   document.addEventListener("extcode:country-updated", (event) => {
     console.log("The event 'extcode:country-updated' was fired.");
     console.log("The event contains the following detail data:");
     console.log(event.detail);
   });



