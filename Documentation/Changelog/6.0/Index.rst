.. include:: ../../Includes.txt

6.0 Changes
===========

.. IMPORTANT::
   **If upgrading from cart version 4.8.1 or earlier: Please read the documentation very carefully! Please make a backup of your filesystem and database!** If possible test the update in a test copy of your TYPO3 instance.

Extracting products to an own cart_products extension
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In the last month, there where a lot of projects where the product table was extended
to the customer needs. In some cases I come to the result that an own product model
respecting the customer domain would be a better solution.
So I decided to extract all product related code to an own extension. If you updating
from Cart v4.x you have to install the cart_products extension as well.

**1. migrate database tables**

.. code-block:: sql

    RENAME TABLE tx_cart_domain_model_product_product TO tx_cartproducts_domain_model_product_product;
    RENAME TABLE tx_cart_domain_model_product_specialprice TO tx_cartproducts_domain_model_product_specialprice;
    RENAME TABLE tx_cart_domain_model_product_quantitydiscount TO tx_cartproducts_domain_model_product_quantitydiscount;
    RENAME TABLE tx_cart_domain_model_product_bevariantattribute TO tx_cartproducts_domain_model_product_bevariantattribute;
    RENAME TABLE tx_cart_domain_model_product_bevariantattributeoption TO tx_cartproducts_domain_model_product_bevariantattributeoption;
    RENAME TABLE tx_cart_domain_model_product_bevariant TO tx_cartproducts_domain_model_product_bevariant;
    RENAME TABLE tx_cart_domain_model_product_fevariant TO tx_cartproducts_domain_model_product_fevariant;
    RENAME TABLE tx_cart_domain_model_product_tag TO tx_cart_domain_model_tag;
    RENAME TABLE tx_cart_domain_model_product_coupon TO tx_cart_domain_model_coupon;
    RENAME TABLE tx_cart_domain_model_product_product_related_mm TO tx_cartproducts_domain_model_product_product_related_mm;
    RENAME TABLE tx_cart_domain_model_product_tag_mm TO tx_cartproducts_domain_model_product_tag_mm;

    ALTER TABLE tx_cart_domain_model_order_address ADD record_type VARCHAR(255) DEFAULT '' NOT NULL;
    ALTER TABLE tx_cart_domain_model_order_address ADD tax_identification_number VARCHAR(255) DEFAULT '' NOT NULL;

    ALTER TABLE tt_content CHANGE COLUMN tx_cart_domain_model_product_product tx_cartproducts_domain_model_product_product int(11) unsigned DEFAULT '0' NOT NULL;

**2. update cart and install cart_products**

Update cart and install cart_product using composer or the update in the extension manager.
Include new TypoScript of cart_products.

**3. migrate some more the database tables**

.. code-block:: sql

    UPDATE tx_cart_domain_model_order_address SET record_type="\Extcode\Cart\Domain\Model\Order\BillingAddress" WHERE discr="billing";
    UPDATE tx_cart_domain_model_order_address SET record_type="\Extcode\Cart\Domain\Model\Order\ShippingAddress" WHERE discr="shipping";

    UPDATE sys_file_reference SET tablenames="tx_cartproducts_domain_model_product_product" WHERE tablenames="tx_cart_domain_model_product_product";

    UPDATE sys_category_record_mm SET tablenames="tx_cartproducts_domain_model_product_product",fieldname="category" WHERE tablenames="tx_cart_domain_model_product_product" AND fieldname="main_category";
    UPDATE sys_category_record_mm SET tablenames="tx_cartproducts_domain_model_product_product" WHERE tablenames="tx_cartproducts_domain_model_product_product" AND fieldname="categories";

**4. update all plugins**

The code summarized different parts of the business logic into a few controller classes. This results in big controller
classes. I decided to split them up in more smaller classes. For an example the updateCountryAction in the the
CartController class was moved to the Cart\CountryController and renamed to updateAction. This encapsulate one part of
the business logic to an controller and thus allows better expandability.
But that has the consequence that all used plugins has to be updated. The update can't be done by a simple query statement.
The following query can help you to find the pages where the plugins are installed.

.. code-block:: sql

    SELECT tt_content.uid, tt_content.pid, pages.title FROM tt_content LEFT JOIN pages ON tt_content.pid = pages.uid WHERE list_type LIKE "cart_%";


It gives you the uid of the *tt_content* element, the page id and the title of the page. You have to update all the plugins
manually through the backend.

A second consequence are the changes in the Templates and Partials directories. The template files was moved to some
subdirectories and the links to controller actions was changed. If you have own cart templates you have to move the
files too.

**5. change checkboxes for acceptTermsAndConditions, acceptRevocationInstruction, and acceptPrivacyPolicy**

In order to link all the documents and get all agreements checked by an own checkbox the two checkboxes
acceptTerms and acceptConditions was summarized in the new acceptTermsAndConditions checkbox. I add two more checkboxes.
One is the agreement to have read and accept the revocation instructions. And the last is for the privace policy.
If you changed the translations or hide one of the old ones, you have to change the TypoScript configuration and
translation for the new ones.

::

    plugin.tx_cart {
        settings {
            validation {
                orderItem {
                    fields {
                        acceptTermsAndConditions.validator = Boolean
                        acceptTermsAndConditions.options.is = true
                        acceptRevocationInstruction.validator = Boolean
                        acceptRevocationInstruction.options.is = true
                        acceptPrivacyPolicy.validator = Boolean
                        acceptPrivacyPolicy.options.is = true
                    }
                }
            }
        }
    }

For more information see: `Checkbox Configuration <../../AdministratorManual/Configuration/Cart/Checkbox/Index.html>`__

**6. check used hooks and signal slots**

If you extend or override classes, use hooks or signal slots you have to check them carefully. They can be removed or
moved to another location.

Sortierung von Frontend- und Backendvarianten
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In der Produktkonfiguration können die Frontend und Backendvarianten sortiert werden.

.. IMPORTANT::
   Es ist eine Aktualisierung der Datenbank erforderlich. Da dieses Feld neu hinzu kommt sind keine Probleme zu erwarten.

Speichern des Rechungs- und Versandländercodes in den Bestelldaten
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In der Bestellung werden nun die Ländercodes aus dem TypoScript gespeichert, um in der späteren Verarbeitung auf die
richtige Konfiguration zugreifen zu können.

.. IMPORTANT::
   Es ist eine Aktualisierung der Datenbank erforderlich. Da dieses Feld neu hinzu kommt sind keine Probleme zu erwarten.

#59 und #64 Füllen der Rechnungs- und Lieferadresse mit Nutzerdaten
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In der showCartAction des Warenkorbplugins wird nun ein Hook *showCartActionAfterCartWasLoaded* bereitgestellt, der
es erlaubt, neben zusätzlichen Änderungen im Warenkorb auch die Rechnungs- und Lieferadresse mit Daten eines
eingeloggten Nutzers vorauszufüllen.

.. IMPORTANT::
   Ein Vorausfüllen der Adressfelder mit Daten aus einem eingeloggten Frontend Benutzer wird es nicht geben.
   Zum einen ist das nicht in jedem Fall gewünscht, zum anderen müsste das FrontendUser Model erweitert werden,
   um alle relevanten Daten eines Nutzers speichern zu können. Oft werden diese Felder schon an anderer Stelle
   bereitgestellt.
