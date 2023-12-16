.. include:: ../../../../Includes.txt

==========
Checkboxes
==========

.. code-block:: typoscript

   plugin.tx_cart {
       settings {
           cart {
               acceptTermsAndConditions.pid = {$plugin.tx_cart.settings.cart.acceptTermsAndConditions.pid}
               acceptRevocationInstruction.pid = {$plugin.tx_cart.settings.cart.acceptRevocationInstruction.pid}
               acceptPrivacyPolicy.pid = {$plugin.tx_cart.settings.cart.acceptPrivacyPolicy.pid}
           }

           validation {
               orderItem {
                   fields {
                       acceptTermsAndConditions.validator = Boolean
                       acceptTermsAndConditions.options.is = true
                       acceptRevocationInstruction.validator = Boolean
                       acceptRevocationInstruction.options.is = true
                       acceptPrivacyPolicy.validator = Boolean
                       acceptPrivacyPolicy.options.is = true
                   }
               }
           }
       }
   }

plugin.tx_cart.settings.cart
============================

.. confval:: acceptTermsAndConditions.pid

   :Type: int
   :Default: empty

   If a page id is given the translation key *tx_cart_domain_model_order_item.accept_terms_and_conditions_with_link*
   will show a checkbox label with a link to the given page.
   If the value is empty the
   *tx_cart_domain_model_order_item.accept_terms_and_conditions_and_conditions*
   translation key will be used in frontend.

.. confval:: acceptRevocationInstruction.pid

   :Type: int
   :Default: empty

   If a page id is given the translation key *tx_cart_domain_model_order_item.accept_revocation_instruction_with_link*
   will show a checkbox label with a link to the given page.
   If the value is empty the
   *tx_cart_domain_model_order_item.accept_revocation_instruction*
   translation key will be used in frontend.

.. confval:: acceptRevocationInstruction.pid

   :Type: int
   :Default: empty

   If a page id is given the translation key *tx_cart_domain_model_order_item.accept_privacy_policy_with_link*
   will show a checkbox label with a link to the given page.
   If the value is empty the
   *tx_cart_domain_model_order_item.accept_privacy_policy*
   translation key will be used in frontend.


plugin.tx_cart.settings.validation.orderItem.fields
===================================================

.. confval:: acceptTermsAndConditions.validator

   :Type: string
   :Default: Boolean

   Add a checkbox for accepting the terms and conditions.
   If validator is not *Boolean* the default template wont render a checkbox.

.. confval:: acceptTermsAndConditions.options.is

   :Type: string
   :Default: true

   Defines which value is expected for terms and conditions checkbox.

.. confval:: acceptRevocationInstruction.validator

   :Type: string
   :Default: Boolean

   Add a checkbox for accepting the revocation instruction.
   If validator is not *Boolean* the default template wont render a checkbox.

.. confval:: acceptRevocationInstruction.options.is

   :Type: string
   :Default: true

   Defines which value is expected for revocation instruction checkbox.

.. confval:: acceptPrivacyPolicy.validator

   :Type: string
   :Default: Boolean

   Add a checkbox for accepting the privacy policy.
   If validator is not *Boolean* the default template wont render a checkbox.

.. confval:: acceptPrivacyPolicy.options.is

   :Type: string
   :Default: true

   Defines which value is expected for privacy policy checkbox.
