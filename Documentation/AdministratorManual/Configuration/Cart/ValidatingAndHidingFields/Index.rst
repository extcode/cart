.. include:: ../../../../Includes.txt

..  _validating-and-hiding-fields:

============================
Validating and hiding fields
============================

Using this TypoScript configuration it is possible to change the validation and
rendering of fields of the billing and shipping address.

The partials for the display of the addresses evaluates this configuration.
It controls whether the fields are mandatory, optional or not rendered att all.

.. code-block:: typoscript
   :caption: EXT:cart/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       settings {
           validation {
               billingAddress {
                   fields {
                       salutation.validator = NotEmpty
                       firstName.validator = NotEmpty
                       lastName.validator = NotEmpty
                       email.validator = NotEmpty
                       phone.validator = Empty
                       fax.validator = Empty
                       street.validator = NotEmpty
                       streetNumber.validator = Empty
                       addition.validator = Empty
                       zip.validator = NotEmpty
                       city.validator = NotEmpty
                   }
               }
               shippingAddress {
                   fields {
                       salutation.validator = NotEmpty
                       firstName.validator = NotEmpty
                       lastName.validator = NotEmpty
                       email.validator = NotEmpty
                       phone.validator = Empty
                       fax.validator = Empty
                       street.validator = NotEmpty
                       streetNumber.validator = Empty
                       addition.validator = Empty
                       zip.validator = NotEmpty
                       city.validator = NotEmpty
                   }
               }
           }
       }
   }

Available validation options
============================

The following examples show the available configuration options.
The examples use the field `salutation` of the invoice address.

Option `Not Empty`
------------------

`NotEmpty` means the field is mandatory. This has the following consequences:

* The field is rendered.
* The label of the field is rendered with a trailing "*".
* The input field is rendered with the attribute `required` set to `true`.

.. code-block:: typoscript

   plugin.tx_cart.settings.validation.billingAddress.fields {
       salutation.validator = NotEmpty
   }

Option `Empty`
--------------

`Empty` means that the field MUST be empty. This has the following consequences:

* The field is not rendered at all.
* The server checks whether the field is empty.

.. code-block:: typoscript

   plugin.tx_cart.settings.validation.billingAddress.fields {
       salutation.validator = Empty
   }

No validation
-------------

This has the following consequences:

* The field is rendered.
* The label of the field is rendered without a trailing "*".
* The input field is rendered without the attribute `required`.

.. code-block:: typoscript

   plugin.tx_cart.settings.validation.billingAddress.fields {
       # !!! It is necessary to remove the whole address field,
       #     not only the `validator` configuration.
       salutation >
   }

.. NOTE::
   The e-mail address should always remain a required field, because it is needed for the ordering process and for sending e-mails.
   If no e-mail address is to be specified, the corresponding EventListeners must be deactivated.
