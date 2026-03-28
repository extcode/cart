<?php

$EM_CONF['cart'] = [
    'title' => 'Cart',
    'description' => 'Shopping Cart(s) for TYPO3',
    'category' => 'plugin',
    'version' => '12.0.0',
    'state' => 'stable',
    'author' => 'Daniel Gohlke',
    'author_email' => 'ext@extco.de',
    'author_company' => 'extco.de UG (haftungsbeschränkt)',
    'constraints' => [
        'depends' => [
            'php' => '8.2.0-8.4.99',
            'typo3' => '14.1.0-14.4.99',
            'extbase' => '14.1.0-14.1.99',
            'fluid' => '14.1.0-14.1.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
