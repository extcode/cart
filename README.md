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

### 2.2 Update and Upgrade

**Attention**, Before updating to a new minor version or upgrading to a new major version, be sure to check the
changelog section in the documentation.
Sometimes minor versions also result in minor adjustments to own templates or configurations.

## 3. Administration

## 3.1 Compatibility and supported Versions

| Cart  | TYPO3      | PHP       | Support/Development                  |
|-------|------------|-----------|--------------------------------------|
| 9.x.x | 12.0       | 8.1+      | Features, Bugfixes, Security Updates |
| 8.x.x | 10.4, 11.5 | 7.2+      | Features, Bugfixes, Security Updates |
| 7.x.x | 10.4       | 7.2 - 7.4 | Security Updates                     |
| 6.x.x | 9.5        | 7.2 - 7.4 |                                      |
| 5.x.x | 8.7        | 7.0 - 7.4 |                                      |
| 4.x.x | 7.6 - 8.7  | 5.6 - 7.2 |                                      |
| 3.x.x | 6.2 - 8.7  | 5.6 - 7.0 |                                      |
| 2.x.x |            |           |                                      |
| 1.x.x |            |           |                                      |

If you need extended support for features and bug fixes outside of the currently supported versions,
we are happy to offer paid services.

### 3.2. Changelog

Please have a look into the [official extension documentation in changelog chapter](https://docs.typo3.org/p/extcode/cart/main/en-us/Changelog/Index.html)

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
