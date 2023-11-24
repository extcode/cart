.. include:: ../../Includes.txt

=====
Hooks
=====

Some more commonly used hooks are:

.. container:: table-row

   Hook Class Name
      `\Extcode\Cart\Controller\Cart\CartController`
   Hook Name
      showCartActionAfterCartWasLoaded
   Description
      Allows adaptions of the cart itself. Furthermore it makes it possible to
      fill the form fields for the bill address and the shipping address with
      data from a logged-in frontend user.

.. container:: table-row

   Hook Class Name
      `\Extcode\Cart\Domain\Model\Cart\BeVariant`
   Hook Name
      changeVariantDiscount
   Description
      Allows to influence the calculation of BE variants.

.. container:: table-row

   Hook Class Name
      `\Extcode\Cart\Service\MailHandler`
   Hook Name
      MailAttachmentsHook
   Description
      Allows you to add attachments to `TYPO3CMSCoreMailMessage`.

.. NOTE::
   The Hooks will be replaced by events in the upcoming major version.
