.. include:: ../../Includes.rst.txt

======================================
Breaking: #611 - Change PHP Dependency
======================================

See `Issue 611 <https://github.com/extcode/cart/issues/611>`__

Description
===========

Tests with newer versions of phpstan under newer php versions showed that the
constructor method `\Extcode\Cart\Domain\Model\Cart\BeVariant::__construct()` uses
required method parameters after optional parameters. This is deprecated since.
PHP 8.0. (see https://php.watch/versions/8.0/deprecate-required-param-after-optional)

As this may lead to an error in a later PHP version, compatibility is restricted
to PHP 8.1-8.4.

Affected Installations
======================

All installations.

Migration
=========

This restriction currently has no effect. No migration is necessary.

.. index:: API
