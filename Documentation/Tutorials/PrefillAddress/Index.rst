.. include:: ../../Includes.rst.txt


.. _prefill-address:

========================================
Prefill address fields for Frontend user
========================================

The billing address during the checkout can be prefilled for a logged-in
Frontend user. You have to register a EventListener which listens to the event
`Extcode\Cart\Event\Cart\BeforeShowCartEvent`.

The following snippets show an example:

.. code-block:: php
   :caption: EXT:cartintegration/Classes/EventListener/BeforeShowCart.php

   <?php

   declare(strict_types=1);

   namespace Vendor\Cartintegration\EventListener\Cart;

   use Extcode\Cart\Event\Cart\BeforeShowCartEvent;
   use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

   final class BeforeShowCart
   {
       public function __invoke(BeforeShowCartEvent $event): void
       {
           $frontendUser = $this->getFrontendUserAuthenticationFromRequest();

           // Hint: The property `user` is marked as internal but it works.
           if (!$frontendUser->user) return;

           $billingAddress = $event->getBillingAddress();

           if ($billingAddress !== null) {
               $billingAddress->setFirstName($frontendUser->user['first_name'] ?? '');
               $billingAddress->setLastName($frontendUser->user['last_name'] ?? '');
               $billingAddress->setEmail($frontendUser->user['email'] ?? '');
               $billingAddress->setStreet($frontendUser->user['address'] ?? '');
               $billingAddress->setZip($frontendUser->user['zip'] ?? '');
               $billingAddress->setCity($frontendUser->user['city'] ?? '');

               // Default to Germany if no country stored in database
               $billingAddress->setCountry($frontendUser->user['country'] ?? 'DE');
           }
       }

       private function getFrontendUserAuthenticationFromRequest(): FrontendUserAuthentication
       {
           $request = $GLOBALS['TYPO3_REQUEST'];
           return $request->getAttribute('frontend.user');
       }
   }


.. code-block:: yaml
   :caption: EXT:cartintegration/Configuration/Services.yaml

   services:
     _defaults:
       autowire: true
       autoconfigure: true
       public: false

     Vendor\Cartintegration\:
       resource: '../Classes/*'
       exclude: '../Classes/Domain/Model/*'

     Vendor\Cartintegration\EventListener\Cart\BeforeShowCart:
       tags:
         - name: event.listener

