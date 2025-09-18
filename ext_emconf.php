<?php

$EM_CONF['cart'] = [
    'title' => 'Cart',
    'description' => 'Shopping Cart(s) for TYPO3',
    'category' => 'plugin',
    'version' => '11.5.0',
    'state' => 'stable',
    'author' => 'Daniel Gohlke',
    'author_email' => 'ext@extco.de',
    'author_company' => 'extco.de UG (haftungsbeschränkt)',
    'constraints' => [
        'depends' => [
            'php' => '8.2.0-8.4.99',
            'typo3' => '13.4.0-13.4.99',
            'extbase' => '13.4.0-13.4.99',
            'fluid' => '13.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
