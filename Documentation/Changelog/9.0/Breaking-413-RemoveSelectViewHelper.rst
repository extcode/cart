.. include:: ../../Includes.txt

============================================================================
Breaking: #413 - Remove \\Extcode\\Cart\\ViewHelpers\\Form\\SelectViewHelper
============================================================================

See :issue:`413`

Description
===========

In Fluid, the SelectViewHelper has been changed to a final class so that the custom SelectViewHelper cannot extend it.
Since the ViewHelper was only used in one place in the order form to output the countries configured in the TypoScript,
the extended ViewHelper is removed.

Affected Installations
======================

All installations are affected by this change.

Migration
=========

If the selection of countries in the billing or shipping address needs to be translated, this can be solved using a
separate partial. Instead of generating the list of options via the ViewHelper, the ViewHelper can also be used with
custom options (see: `Custom options and option group rendering <https://docs.typo3.org/other/typo3/view-helper-reference/11.5/en-us/typo3/fluid/latest/Form/Select.html#custom-options-and-option-group-rendering>`__).
Since it is an array, the options can be inserted by iterating over all elements of the array and translating them
with <f:translate>.

.. index:: Template, Frontend
