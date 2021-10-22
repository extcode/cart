.. include:: ../../Includes.txt

=================================================
Breaking: #288 - Change MailHandler to FluidEmail
=================================================

See :issue:`288`

Description
===========

The changeover from rendering an own standalone view to the FluidEmail
class required adjustments in the e-mail templates and partials.

Affected Installations
======================

All installations that use their own templates and partials for the e-mails.
All installations that use an own layout file for the e-mails.

Migration
=========

To be able to use your own templates and parts, the folders where the
files are located must be included via `$GLOBALS['TYPO3_CONF_VARS']['MAIL']`.
The extension has included the following in the ext_localconf.php

::

   $GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths']['1588829280'] = 'EXT:cart/Resources/Private/Templates/';
   $GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths']['1588829280'] = 'EXT:cart/Resources/Private/Partials/';

To ensure the correct order, the own key (UNIX timestamp) must be larger.

Furthermore you should compare your own templates and partials with those of the extension.

.. index:: API, Frontend, Backend
