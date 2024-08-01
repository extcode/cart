<?php

$EM_CONF['cart'] = [
    'title' => 'Cart',
    'description' => 'Shopping Cart(s) for TYPO3',
    'category' => 'plugin',
    'version' => '10.0.0',
    'state' => 'stable',
    'author' => 'Daniel Gohlke',
    'author_email' => 'ext@extco.de',
    'author_company' => 'extco.de UG (haftungsbeschränkt)',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
