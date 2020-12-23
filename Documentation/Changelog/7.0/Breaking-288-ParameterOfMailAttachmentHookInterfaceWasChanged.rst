.. include:: ../../Includes.txt

========================================================================
Breaking: #288 - Parameter of Mail Attachment Hook Interface was Changed
========================================================================

See :issue:`288`

Description
===========

Now that the extension has been changed to `\TYPO3\CMS\Core\Mail\FluidEmail` instead of `\TYPO3\CMS\Core\Mail\MailMessage`,
the attachments have to be handled a bit differently.
For this purpose, the first passed parameter and the return parameter have been changed to `\TYPO3\CMS\Core\Mail\FluidEmail`.

Affected Installations
======================

Only installations that have registered their own class on the MailAttachmentsHook and thus add their
own files to the email are affected.

Migration
=========

The classes for the first transfer parameter and the return parameter are to be replaced.
It may also be necessary to make adjustments to the class if functions were called on the previous
`\TYPO3\CMS\Core\Mail\MailMessage` class that are not available in the `\TYPO3\CMS\Core\Mail\FluidEmail` class.

.. index:: Backend
