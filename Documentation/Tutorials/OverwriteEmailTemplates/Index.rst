.. include:: ../../Includes.rst.txt

.. _overwrite-email-templates:

=========================
Overwrite email templates
=========================

If you want change the content of emails which are sent during an order you need
to overwrite the give templates/partials.

.. code-block:: php
   :caption: EXT:sitepackage/ext_localconf.php

   $GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths']['1588829281'] = 'EXT:sitepackage/Resources/Private/Partials/';
   $GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths']['1588829281'] = 'EXT:sitepackage/Resources/Private/Templates/';

The files need then to be placed in a directory named `Mail`.

The following tree view shows an example how the overwrite the mail template
which is used when sending an email to the customer (=buyer) when the order is not paid yet
("open"). Furthermore the address partial is overwritten.

.. code-block:: typoscript
   :caption: Treeview under EXT:sitepackage/Resources/Private when using the above given configuration in `ext_localconf.php`

   ├── Partials
   │   └── Mail
   │       └── Address.html
   └── Templates
       └── Mail
           └── Open
               └── Buyer.html

.. _overwrite-email-templates-status:

Templates per payment status
============================

The template of the emails to the buyer and the seller is chosen by the payment
status of the order when the order is finished: the identifier of the status
with an upper case first letter is used as directory, e.g. `Mail/Paid/Buyer.html`
for the status `paid`. EXT:cart ships templates for the status `open` and
`paid` only.

If an extension registers an own payment status (see
:ref:`developer-order-status`) and the order can have this status when it is
finished, e.g. set by a payment provider, add the templates for it as well,
e.g. for the status `authorized`:

.. code-block:: text
   :caption: Treeview under EXT:my_extension/Resources/Private

   └── Templates
       └── Mail
           └── Authorized
               ├── Buyer.html
               └── Seller.html
