.. include:: ../../Includes.rst.txt

.. _developer-forms:

==================
Forms for products
==================

A product extension can link its products with forms of the form framework
(EXT:form). The customer fills in the form, e.g. the names and the age of the
participants of an event, and the product is added to the cart with these
values. EXT:cart_events uses this to book events with a form.

In your own product extension you are free to add your own normal fields for
values of the product, e.g. the color of the product. The form framework is
useful when the editor defines the fields per product, product type or product
group: every form can have its own fields with different input types and
validators, without changing the templates of the product extension.

EXT:cart does not use these forms itself; it provides the parts a product
extension needs:

*  the form prototype ``cart`` with the finisher ``AddToCart``,
*  the event ``\Extcode\Cart\Event\Form\AddToCartFinisherEvent`` to create the
   products from the form values,
*  the itemsProcFunc ``\Extcode\Cart\Hooks\FormDefinitions`` to select a form
   for a product in the backend.

.. note::

   The package ``typo3/cms-form`` is not required by EXT:cart; the product
   extension has to require it.

Form prototype
==============

EXT:cart registers the prototype ``cart`` in
:file:`EXT:cart/Configuration/Yaml/FormSetup.yaml`. The YAML file is included
with the TypoScript of EXT:cart for the frontend and the backend. The
prototype inherits from the prototype ``standard`` and

*  adds the finisher ``AddToCart``,
*  removes the finishers of the prototype ``standard`` (``EmailToSender``,
   ``EmailToReceiver``, ``Redirect``, ``DeleteUploads`` and ``Confirmation``)
   from the selection in the form editor, because sending mails or
   redirecting to a page makes no sense when a product is added to the cart,
*  uses its own form template, which allows submitting the form by AJAX.

The finisher ``AddToCart`` cannot be selected in the form editor either. It is
part of the template for new forms, so an editor cannot remove it by accident.

The prototype ``cart`` is the base for the prototypes of the product
extensions and should not be used for forms directly. A product extension adds
its own prototype which inherits from ``cart``. This way the own prototype
inherits the finisher and the restricted selection of finishers, and only the
forms of this prototype can be selected for the products of the extension.

.. important::

   The name of the prototype connects the parts of the product extension and
   has to be the same in three places:

   #. the identifier of the prototype and of the selectable prototype in the
      form setup,
   #. the ``prototypeName`` of the template for new forms,
   #. the ``prototypeName`` in the ``itemsProcFuncConfig`` of the field
      ``form_definition``.

   EXT:cart_events uses the prototype ``cart-events``, see
   :file:`EXT:cart_events/Configuration/Yaml/FormSetup.yaml`,
   :file:`EXT:cart_events/Resources/Private/Backend/Templates/FormEditor/Yaml/NewForms/CartEventsExample.form.yaml`
   and
   :file:`EXT:cart_events/Configuration/TCA/Overrides/tx_cartevents_domain_model_event.php`.

The form setup registers the prototype and makes it selectable in the backend
module "Forms", with a template for new forms:

.. code-block:: yaml
   :caption: EXT:my_product/Configuration/Yaml/FormSetup.yaml

   TYPO3:
     CMS:
       Form:
         formManager:
           selectablePrototypesConfiguration:
             1700000000:
               identifier: 'my-product'
               label: 'My product'
               newFormTemplates:
                 100:
                   templatePath: 'EXT:my_product/Resources/Private/Backend/Templates/FormEditor/Yaml/NewForms/Example.form.yaml'
                   label: 'My product form (Example)'
         prototypes:
           my-product:
             __inheritances:
               10: 'TYPO3.CMS.Form.prototypes.cart'

The form setup has to be added to the YAML configurations of the form
framework for the frontend and the backend, like EXT:cart does in its
TypoScript:

.. code-block:: typoscript
   :caption: EXT:my_product/Configuration/TypoScript/setup.typoscript

   plugin.tx_form.settings.yamlConfigurations {
       1700000000 = EXT:my_product/Configuration/Yaml/FormSetup.yaml
   }

   module.tx_form.settings.yamlConfigurations {
       1700000000 = EXT:my_product/Configuration/Yaml/FormSetup.yaml
   }

The template for new forms uses the same prototype and contains the finisher
``AddToCart``. The editor adds the fields in the backend module "Forms":

.. code-block:: yaml
   :caption: EXT:my_product/Resources/Private/Backend/Templates/FormEditor/Yaml/NewForms/Example.form.yaml

   renderingOptions:
     submitButtonLabel: Submit
     controllerAction: form
   type: Form
   identifier: my-product-example
   label: 'My product - Example'
   prototypeName: my-product
   finishers:
     -
       identifier: AddToCart
   renderables:
     -
       type: Page
       identifier: page-1
       label: ''
       renderables:
         -
           type: Text
           identifier: firstName
           label: FirstName
           validators:
             -
               identifier: NotEmpty

Select a form for a product
===========================

The field which stores the selected form has to be named ``form_definition``.
The itemsProcFunc lists the forms of the prototype configured in
``itemsProcFuncConfig``. Without this configuration it lists the forms of the
prototype ``cart``, so the prototype of the product extension should always be
configured. The field should only be added if EXT:form is loaded:

.. code-block:: php
   :caption: EXT:my_product/Configuration/TCA/Overrides/tx_myproduct_domain_model_product.php

   use Extcode\Cart\Hooks\FormDefinitions;
   use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

   if (ExtensionManagementUtility::isLoaded('form')) {
       ExtensionManagementUtility::addTCAcolumns(
           'tx_myproduct_domain_model_product',
           [
               'form_definition' => [
                   'label' => 'Form',
                   'config' => [
                       'type' => 'select',
                       'renderType' => 'selectSingle',
                       'items' => [
                           ['label' => '', 'value' => ''],
                       ],
                       'itemsProcFunc' => FormDefinitions::class . '->getItems',
                       'itemsProcFuncConfig' => [
                           'prototypeName' => 'my-product',
                       ],
                       'size' => 1,
                       'minitems' => 0,
                       'maxitems' => 1,
                       'softref' => 'formPersistenceIdentifier',
                   ],
               ],
           ]
       );
       ExtensionManagementUtility::addToAllTCAtypes(
           'tx_myproduct_domain_model_product',
           'form_definition'
       );
   }

The forms of the file storages are filtered by the prototype of their
listing. The database storage lists every form with the prototype
``standard``; these forms are loaded to check their prototype.

Create the products
===================

When the form is submitted, the finisher ``AddToCart`` dispatches the event
``\Extcode\Cart\Event\Form\AddToCartFinisherEvent`` with the form values and
the cart. A listener of the product extension creates the products from the
form values and passes them back to the event. The finisher adds the products
to the cart and stores the cart in the session.

.. code-block:: php
   :caption: EXT:my_product/Classes/EventListener/AddProductFromForm.php

   use Extcode\Cart\Event\Form\AddToCartFinisherEvent;

   final class AddProductFromForm
   {
       public function __invoke(AddToCartFinisherEvent $event): void
       {
           $formValues = $event->getFormValues();
           if (($formValues['productType'] ?? '') !== 'MyProduct') {
               return;
           }

           // Create the \Extcode\Cart\Domain\Model\Cart\Product from the form
           // values, other form values can be added as frontend variants.
           $cartProduct = ...;

           $event->setCartProducts([$cartProduct]);
           // or, if the product cannot be added:
           // $event->setErrors([...]);
       }
   }

The listener is registered for the event in the service configuration of the
product extension. Several product extensions can listen to the event, so a
listener should only handle the forms of its own products, e.g. by a hidden
field ``productType`` in the form.

The finisher responds with JSON for the page types configured in
``plugin.tx_cart.settings.jsonResponseForPageTypes``, otherwise it adds a
flash message. The messages are taken from the language labels
``tx_cart.add_to_cart_finisher.<productType>.message.status.200.body`` and
``.title``, with a fallback to
``tx_cart.add_to_cart_finisher.message.status.200.body`` and ``.title``.
