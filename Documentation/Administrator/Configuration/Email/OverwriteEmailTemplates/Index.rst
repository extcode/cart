.. include:: ../../../../Includes.rst.txt

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
which is used when sending an email to the customer (=buyer) when the order is not payed yet
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
