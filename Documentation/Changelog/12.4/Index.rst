.. include:: ../../Includes.rst.txt

12.4 Changes
============

**Table of contents**

.. contents::
   :local:
   :depth: 1

12.4.0
------

Breaking
~~~~~~~~

*  :doc:`#843 - Constructors of the attachment listeners <Breaking-843-AttachmentListeners>`

Features
~~~~~~~~

*  :doc:`#836 - Register own payment and shipping status <Feature-836-RegisterOwnOrderStatus>`
*  :doc:`#838 - Protect status changes and keep an order history <Feature-838-StatusChangeAndOrderHistory>`
*  :doc:`#840 - Events before and after a status change of orders <Feature-840-EventsOfStatusChanges>`
*  :doc:`#843 - Emails on status changes of orders <Feature-843-StatusChangeEmails>`

Deprecations
~~~~~~~~~~~~

*  :doc:`#836 - Payment and shipping status in the TCA items <Deprecation-836-OrderStatusInTcaItems>`
*  :doc:`#845 - MailHandler <Deprecation-845-MailHandler>`
*  :doc:`#847 - Site setting extcode.taxclasses <Deprecation-847-SiteSettingTaxClasses>`
*  :doc:`#848 - Site settings extcode.cart-payments and shippings <Deprecation-848-SiteSettingsPaymentsShippings>`

Bugfixes
~~~~~~~~

*  Read the tax classes of the site set extcode/cart-taxclasses (`#850 <https://github.com/extcode/cart/pull/850>`__)
*  Use the payment and shipping methods of de for at and ch in the site sets (`#851 <https://github.com/extcode/cart/pull/851>`__)
*  Redirect to the success URL of the payment method after an order (`#853 <https://github.com/extcode/cart/pull/853>`__)
*  Send the order emails if the payment method is not configured (`#855 <https://github.com/extcode/cart/pull/855>`__)
*  Use the country of the shipping address in the SiteSets ShippingMethodsLoader (`#857 <https://github.com/extcode/cart/pull/857>`__)
*  Subtract coupon taxes only once in Cart::getTotalTaxes() (`#859 <https://github.com/extcode/cart/pull/859>`__)
*  Sum up taxes of tax classes without products (`#859 <https://github.com/extcode/cart/pull/859>`__)

.. toctree::
   :hidden:
   :glob:

   *-*
