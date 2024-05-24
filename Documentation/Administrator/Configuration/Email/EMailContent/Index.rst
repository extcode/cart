.. include:: ../../../../Includes.txt

====================
Static email content
====================

It is possible to add content to emails which can be edited in the TYPO3
backend.

1. Create a content element which has inputs for the fields `header` and
   `bodytext`, e.g. the default content element `Textmedia`.

2. The `uid` of this content element can then be set in as TypoScript constant.

The available fields are:

.. code-block:: typoscript
   :caption: EXT:sitepackage/Configuration/TypoScript/constants.typoscript

   plugin.tx_cart.uids.lib {

      # Use below the salutation and thank you for your order message but
      # above the order message.
      cartMailHeader =

      # Used below the order message in the here given order.
      cartMailFooter =
      cartMailFooterSignature =
      cartMailFooterTermsOfService =
      cartMailFooterRevocation =
      cartMailFooterDataProtection =
      cartMailFooterImpress =
   }
