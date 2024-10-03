.. include:: ../../Includes.rst.txt

==========================================
Deprecation: 392 - Add Percentage Discount
==========================================

See `Issue 392 <https://github.com/extcode/cart/issues/392>`__

Description
===========

In order to be able to implement custom voucher types, the voucher type
selection will no longer contain a string, but the class that implements that
voucher. This requires some changes to the
`\Extcode\Cart\Domain\Model\Cart\CouponInterface`, which are not finalized at
this point.

What is certain is that there will be a defined constructor method. Also the
method `getTax()` will be dropped and instead there will be a method
`getTaxes()` which has to return an array instead of a float value. For custom
coupons implementing the `\Extcode\Cart\Domain\Model\Cart\CartCouponInterface`
or overriding the existing implementation, adaptations have to be planned with
the upgrade to version v9 for TYPO3 v12 and v11.

Furthermore there will be an upgrade wizard, which replaces the existing string
"cartcoupon" in the `coupon_type` against the class
`\Extcode\Cart\Domain\Model\Cart\CouponFix`.

Impact
======

.. index:: API, Backend, TCA
