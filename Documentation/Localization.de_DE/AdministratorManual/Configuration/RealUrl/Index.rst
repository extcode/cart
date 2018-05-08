.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

RealURL
=======

Lesbare URLs sind Ziel eines jeden Kunden und oft wird dafür RealUrl eingesetzt. Da die Konfiguration nicht gerade einfach ist,
soll hier eine Beispielkonfiguration gezeigt werden:

::

    'fixedPostVars' => [
        'cartShowCart' => [
            [
                'GETvar' => 'tx_cart_cart[action]',
                'valueMap' => array(
                    '' => 'showCart',
                )
            ],
            [
                'GETvar' => 'tx_cart_cart[controller]',
                'noMatch' => 'bypass'
            ]
        ],
        'cartShowProduct' => [
            [
                'GETvar' => 'tx_cart_product[product]',
                'lookUpTable' => [
                    'table' => 'tx_cart_domain_model_product_product',
                    'id_field' => 'uid',
                    'alias_field' => 'title',
                    'addWhereClause' => ' AND NOT deleted',
                    'useUniqueCache' => 1,
                    'useUniqueCache_conf' => [
                        'strtolower' => 1,
                        'spaceCharacter' => '-'
                    ],
                    'languageGetVar' => 'L',
                    'languageExceptionUids' => '',
                    'languageField' => 'sys_language_uid',
                    'transOrigPointerField' => 'l10n_parent',
                    'autoUpdate' => 1,
                    'expireDays' => 180,
                ]
            ],
            [
                'GETvar' => 'tx_cart_product[controller]',
                'noMatch' => 'bypass'
            ]
        ],
    ],

|

Diese Variablen müssten nun jeder Seite zugeordnet werden, in der sich das Warenkorb-Plugin oder das Produkt-Plugin (Detailseite)
befindet. Für Redakteure ist dies so nicht zu pflegen, so dass es die Möglichkeit gibt, den Seiten in den Seiteneigenschaften
einen eigenen Seitentyp "Cart: Produkt" oder "Cart: Warenkorb" zu geben. Ein Hook wertet diesen Seitentyp aus und erzeugt einen
entsprechenden Eintrag. Ein Redakteur kann dann neue Produktdetailseiten, z.B. für eine neue Kategorie, anlegen und muss dann
lediglich diesen Seitentyp zuordnen.

Die verwendeten Doktypes sind:

- cartShowCart: 181
- cartShowProduct: 182
