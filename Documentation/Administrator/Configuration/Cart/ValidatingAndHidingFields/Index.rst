.. include:: ../../../../Includes.txt

..  _validating-and-hiding-fields:

============================
Validating and hiding fields
============================

Using the below shown TypoScript configuration allows to change the validation
and rendering of fields of the billing and shipping address.
The partials for the display of the addresses evaluates this configuration.
It controls whether the fields are mandatory, optional or not rendered at all.

.. TIP::
   This way it is easily possible to unhide e.g the fields `streetNumber` or
   `addition` (which is a field for additional address information) which are
   already defined but hidden by default.

   Have a look at all fields with the value `Empty` to see all available
   fields.


.. code-block:: typoscript
   :caption: EXT:cart/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       settings {
           validation {
               billingAddress {
                   fields {
                       salutation.validator = NotEmpty
                       title.validator = Empty
                       firstName.validator = NotEmpty
                       lastName.validator = NotEmpty
                       email.validator = NotEmpty
                       phone.validator = Empty
                       fax.validator = Empty
                       company.validator = Empty
                       taxIdentificationNumber.validator = Empty
                       street.validator = NotEmpty
                       streetNumber.validator = Empty
                       addition.validator = Empty
                       zip.validator = NotEmpty
                       city.validator = NotEmpty
                       country.validator = NotEmpty
                   }
               }
               shippingAddress {
                   fields {
                       salutation.validator = NotEmpty
                       title.validator = Empty
                       firstName.validator = NotEmpty
                       lastName.validator = NotEmpty
                       email.validator = NotEmpty
                       phone.validator = Empty
                       fax.validator = Empty
                       company.validator = Empty
                       taxIdentificationNumber.validator = Empty
                       street.validator = NotEmpty
                       streetNumber.validator = Empty
                       addition.validator = Empty
                       zip.validator = NotEmpty
                       city.validator = NotEmpty
                       country.validator = NotEmpty
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
   :caption: EXT:sitepackage/Configuration/TypoScript/constants.typoscript

   plugin.tx_cart.settings.validation.billingAddress.fields {
       salutation.validator = NotEmpty
   }

Option `Empty`
--------------

`Empty` means that the field MUST be empty. This has the following consequences:

* The field is not rendered at all.
* The server checks whether the field is empty.

.. code-block:: typoscript
   :caption: EXT:sitepackage/Configuration/TypoScript/constants.typoscript

   plugin.tx_cart.settings.validation.billingAddress.fields {
       salutation.validator = Empty
   }

No validation
-------------

This has the following consequences:

* The field is rendered.
* The label of the field is rendered without a trailing "*".
* The input field is rendered without the attribute `required`.

.. IMPORTANT::
   While the two configuration above can be made within the
   `constants.typoscript` is this configuration done in `setup.typoscript`.

.. code-block:: typoscript
   :caption: EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart.settings.validation.billingAddress.fields {
       # !!! It is necessary to remove the whole address field,
       #     not only the `validator` configuration.
       salutation >
   }

.. NOTE::
   The email address should always remain a required field,
   because it is needed for the ordering process and for sending emails.
   If no e-mail address is to be specified, the corresponding EventListeners must be deactivated.
