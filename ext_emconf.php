<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Cart',
    'description' => 'Shopping Cart(s) for TYPO3',
    'category' => 'plugin',
    'shy' => false,
    'version' => '1.1.4',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'module' => '',
    'state' => 'beta',
    'uploadfolder' => false,
    'createDirs' => 'uploads/tx_cart/order_pdf,uploads/tx_cart/invoice_pdf',
    'modify_tables' => '',
    'clearcacheonload' => true,
    'lockType' => '',
    'author' => 'Daniel Lorenz',
    'author_email' => 'ext.cart@extco.de',
    'author_company' => 'extco.de UG (haftungsbeschränkt)',
    'CGLcompliance' => null,
    'CGLcompliance_note' => null,
    'constraints' => [
        'depends' => [
            'typo3' => '6.2.0-7.99.99',
            'php' => '5.4.0'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
