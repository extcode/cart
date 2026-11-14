<?php

declare(strict_types=1);

$formDefinition = static fn (int $uid, string $prototypeName, int $deleted = 0): array => [
    'uid' => $uid,
    'pid' => 0,
    'deleted' => $deleted,
    'identifier' => 'form-' . $uid,
    'label' => 'Form ' . $uid,
    'configuration' => [
        'type' => 'Form',
        'identifier' => 'form-' . $uid,
        'label' => 'Form ' . $uid,
        'prototypeName' => $prototypeName,
        'renderables' => [],
    ],
];

return [
    'form_definition' => [
        $formDefinition(1, 'standard'),
        $formDefinition(2, 'cart'),
        $formDefinition(3, 'cart-events'),
        $formDefinition(4, 'cart', 1),
        $formDefinition(5, 'cart'),
    ],
];
