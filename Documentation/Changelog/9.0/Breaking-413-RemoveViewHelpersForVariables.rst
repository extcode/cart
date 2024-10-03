.. include:: ../../Includes.rst.txt

=================================================
Breaking: #413 - Remove ViewHelpers for Variables
=================================================

See `Issue 413 <https://github.com/extcode/cart/issues/413>`__

Description
===========

The extension does not use the ViewHelper for variables. In TYPO3 a ViewHelper is available for setting and reading
variables. So the own implementation can be removed without disadvantages.

Affected Installations
======================

All installations using `\Extcode\Cart\ViewHelpers\Variable\GetViewHelper` or `\Extcode\Cart\ViewHelpers\Variable\SetViewHelper`
in templates or partials.

Migration
=========

Replace all occurrences in the templates.

.. index:: Template, Frontend
