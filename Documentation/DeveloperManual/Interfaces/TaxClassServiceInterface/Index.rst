.. include:: ../../../Includes.txt

..  _tax_class_service_interface:

TaxClassServiceInterfaces
=========================

The `\Extcode\Cart\Service\TaxClassServiceInterface` allows you to load the tax classes for the shopping cart via an
interface.

EXT:cart uses TypoScript for the configuration of the tax classes by default.
The implementation `\Extcode\Cart\Service\TaxClassService` of this interface parsing the TypoScript and generates
the array of tax classes.

A separate class for loading the tax classes can be configured via TypoScript as follows:

::

   plugin.tx_cart {
       taxClasses >
       taxClasses.className = Vendor\Extension\Service\TaxClassService
   }

The class have to implement the interface and should return an array of `\Extcode\Cart\Domain\Model\Cart\TaxClass`.
To avoid exceptions all tax classes of the shop must be returned.
No tax classes (e.g. reduced, free) need to be returned if neither products nor payment and shipping methods use them.
