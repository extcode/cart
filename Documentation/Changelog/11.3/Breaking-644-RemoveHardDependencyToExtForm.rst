.. include:: ../../Includes.rst.txt

===================================================
Breaking: #644 - Remove hard dependency to EXT:form
===================================================

See `Issue 644 <https://github.com/extcode/cart/issues/644>`__

Description
===========

`extcode/cart` itself doesn't need any code from `typo3/cms-form`.

This extension provide an itemProcFunc to provide a list of the form definition
for a selector filtered by a given prototype.
This extension provide an AddToCartFinisher which extends the AbstractFinisher
of `typo3/cms-form`.

Both classes can be used by product extensions, but are not used by `extcode/cart`
itself.

To achieve this, the part concerning the form definitions had to be moved to a
separate class `\Extcode\Cart\Hooks\FormDefinitions`.

Affected Installations
======================

Own product extensions that link products with form_definitions and have used
the itemProcFunc ‘Extcode\\Cart\\Hooks\\ItemsProcFunc->user_formDefinition’ in
the TCA.

Migration
=========

Replace

.. code-block:: php

    'itemsProcFunc' => 'Extcode\\Cart\\Hooks\\ItemsProcFunc->user_formDefinition',
    'itemsProcFuncConfig' => [
        'prototypeName' => 'cart-events',
    ],

with

.. code-block:: php

    'itemsProcFunc' => \\Extcode\\Cart\\Hooks\\FormDefinitions::class . '->getItems',
    'itemsProcFuncConfig' => [
        'prototypeName' => 'cart-events',
    ],
