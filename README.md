[![Build Status](https://travis-ci.org/extcode/cart.svg?branch=master)](https://travis-ci.org/extcode/cart)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/5b5b6e0c8ac143c381026061abf3c9e8)](https://www.codacy.com/app/extcode/cart?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=extcode/cart&amp;utm_campaign=Badge_Grade)

The extension is a small but powerful extension which "solely" adds a shopping cart to your TYPO3 installation and is
well suited for content commerce.

The extension allows you to add products to a cart and handles the order process completely.
There are other awesome extensions like `extcode/cart-products`, `extcode/cart-events`, and `extcode/cart-books` to
handle different types of products.

Furthermore, you will find some payment provider extensions like `extcode/cart-payone`, `extcode/cart-paypal`,
`extcode/cart-saverpay`, and more to add payment methods to the checkout process.

## 1. Features

- makes intensive use of the TYPO3 Core API functionality
- very well expandable
  - several hooks, signal slots, and interfaces
  - API (finisher pipeline) to process the order with possibility to register own tasks
  - API to add payment providers
  - API to connect your own product extensions
- highly configurable through TypoScript
- proved Bootstrap templates
- backend module to show and utilize orders

## 2. Installation / Upgrade

### 2.1 Installation

#### Installation using Composer

The recommended way to install the extension is by using [Composer][2].
In your Composer based TYPO3 project root, just do `composer require extcode/cart`. 

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install the extension with the extension manager module.

### 2.2 Upgrade

**Attention**, Cart version 6.x brings a lot of features and changes.
Not all changes are fully documented. Some of the changes may breaking your
shop integration. especially when you extend Cart with own features.

**If upgrading from cart version 4.8.1 or earlier: Please read the documentation very carefully! Please make a backup of your filesystem
and database!** If possible test the update in a test copy of your TYPO3 instance.

## 3. Administration

## 3.1 Compatibility and supported Versions

| Cart          | TYPO3      | PHP       | Support/Development                     |
| ------------- | ---------- | ----------|---------------------------------------- |
| 7.x.x         | 10.4       | 7.2 - 7.4 | Features, Bugfixes, Security Updates    |
| 6.x.x         | 9.5        | 7.2 - 7.4 | Features _(in certain circumstances with feature toogle)_, Bugfixes, Security Updates    |
| 5.x.x         | 8.7        | 7.0 - 7.4 | Bugfixes, Security Updates              |
| 4.x.x         | 7.6 - 8.7  | 5.6 - 7.2 | Security Updates                        |
| 3.x.x         | 6.2 - 8.7  | 5.6 - 7.0 | Security Updates                        |
| 2.x.x         |            |           |                                         |
| 1.x.x         |            |           |                                         |

### 3.2. Changelog

Please have a look into the [official extension documentation in changelog chapter](https://docs.typo3.org/typo3cms/extensions/cart/Changelog/Index.html)

### 3.3. Release Management

News uses **semantic versioning** which basically means for you, that
- **bugfix updates** (e.g. 1.0.0 => 1.0.1) just includes small bugfixes or security relevant stuff without breaking changes.
- **minor updates** (e.g. 1.0.0 => 1.1.0) includes new features and smaller tasks without breaking changes.
- **major updates** (e.g. 1.0.0 => 2.0.0) breaking changes wich can be refactorings, features or bugfixes.

## 4. Sponsoring

* Ask for an invoice.
* [GitHub Sponsors](https://github.com/sponsors/extcode)
* [PayPal.Me](https://paypal.me/extcart)
* [Patreon](https://patreon.com/ext_cart)

[1]: https://docs.typo3.org/typo3cms/extensions/cart/
[2]: https://getcomposer.org/