.. include:: ../../../../Includes.txt

..  _validation_configuration:

Validation Configuration
========================

Using this TypoScript configuration it is possible to change the validation for fields of the billing as well as shipping address.
The partials for the display of the addresses evaluates this configuration if necessary and regulates both the output of the fields and whether the respective field is a mandatory field.

::

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


Exemplarily the configuration possibilities of the fields are to be shown at the example of the field `salutation` of the invoice address.

The default configuration validates the field created as mandatory.
In the output there is a `*` for mandatory fields. At the input field `required` is set to true.

::

   plugin.tx_cart {
       settings {
           validation {
               billingAddress {
                   fields {
                       salutation.validator = NotEmpty
                   }
               }
           }
       }
   }


In contrast, you can also use `Empty` instead of `NotEmpty`. The server then checks whether the field is empty. However, this option also ensures that the field is not rendered at all, i.e. it does not appear in the frontend.

::

   plugin.tx_cart {
       settings {
           validation {
               billingAddress {
                   fields {
                       salutation.validator = NotEmpty
                   }
               }
           }
       }
   }


If no validation is set for a field, the field is output without `*`. The `required` attribute is not set. To remove the validation of fields from the default configuration you can use the `>` of TypoScript.
It is necessary to remove the whole address field, not only the `validator` configuration.

::

   plugin.tx_cart {
       settings {
           validation {
               billingAddress {
                   fields {
                       salutation >
                   }
               }
           }
       }
   }

.. NOTE::
   The e-mail address should always remain a required field, because it is needed for the ordering process and for sending e-mails.
   If no e-mail address is to be specified, the corresponding EventListeners must be deactivated.
