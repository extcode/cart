.. include:: ../../Includes.txt

=====
Hooks
=====

.. NOTE::
   The Hooks will be replaced by events in the upcoming major version.

.. confval:: \Extcode\Cart\Controller\Cart\CartController

   **Hook Name:** `showCartActionAfterCartWasLoaded`

   Allows adaptions of the cart itself. Furthermore it makes it possible to
   fill the form fields for the bill address and the shipping address with
   data from a logged-in frontend user.

.. confval:: \Extcode\Cart\Domain\Model\Cart\BeVariant

   **Hook Name:** `changeVariantDiscount`

   Allows to influence the calculation of BE variants.

.. confval:: \Extcode\Cart\Service\MailHandler

   **Hook Name:** `MailAttachmentsHook`

   Allows you to add attachments to `TYPO3CMSCoreMailMessage`.

