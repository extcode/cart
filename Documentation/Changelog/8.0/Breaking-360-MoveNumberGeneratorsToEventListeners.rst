.. include:: ../../Includes.rst.txt

=========================================================
Breaking: #360 - Move Number Generators To EventListeners
=========================================================

See :issue:`360`

Description
===========

The generation of order, invoice and delivery note numbers has been moved
from `Extcode\Cart\Utility\OrderUtility::class` to its own EventListener.
The generation is also no longer done via TypoScript and can also no longer
be configured via TypoScript.

Affected Installations
======================

All installations are affected by this change.

Migration
=========

If the generation of order numbers via TypoScript has been configured
individually (e.g. with prefix or suffix, offset, leading zeros), this
configuration must be transferred to the registration of the service.
On the :ref:`Number Generator Configuration <number-generator-configuration>`
page is documented how this can be done. If other elements, such as the
inclusion of or parts of the date, are used via TypoScript, this must
be implemented via a separate EventListener.

.. index:: Frontend, Backend
