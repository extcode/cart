<?php

declare(strict_types=1);

namespace Extcode\Cart\OrderStatus;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Service\DependencyOrderingService;
use UnexpectedValueException;

/**
 * Validates, merges and orders the payment and shipping status of the
 * Configuration/OrderStatus.php files of all packages.
 *
 * A package may change a status of another package by using the same
 * identifier, the given keys replace the ones of the earlier package.
 */
final readonly class OrderStatusConfigurationBuilder
{
    private const ALLOWED_KEYS = ['label', 'before', 'after'];

    private const IDENTIFIER_PATTERN = '/^[a-z][a-z0-9_]*$/';

    public function __construct(
        private DependencyOrderingService $dependencyOrderingService,
    ) {
    }

    /**
     * @param array<string, mixed> $configurations the return values of Configuration/OrderStatus.php by package key, in the order of the packages
     *
     * @return array<value-of<ServiceType>, array<string, string>> the labels by identifier in the order of the status, for each service type
     */
    public function build(array $configurations): array
    {
        $merged = [];
        foreach (ServiceType::cases() as $serviceType) {
            $merged[$serviceType->value] = [];
        }

        foreach ($configurations as $packageKey => $configuration) {
            if (!is_array($configuration)) {
                throw new UnexpectedValueException(
                    'The Configuration/OrderStatus.php of the package "' . $packageKey . '" must return an array.',
                    1795068606
                );
            }

            foreach ($configuration as $serviceTypeValue => $statuses) {
                $serviceType = ServiceType::tryFrom((string) $serviceTypeValue);
                if ($serviceType === null) {
                    throw new UnexpectedValueException(
                        'The Configuration/OrderStatus.php of the package "' . $packageKey . '" contains the unknown service type "' . $serviceTypeValue . '", allowed are "' . implode('", "', array_column(ServiceType::cases(), 'value')) . '".',
                        1795068607
                    );
                }
                if (!is_array($statuses)) {
                    throw new UnexpectedValueException(
                        'The ' . $serviceType->value . ' status of the Configuration/OrderStatus.php of the package "' . $packageKey . '" must be an array.',
                        1795068608
                    );
                }

                foreach ($statuses as $identifier => $definition) {
                    $identifier = (string) $identifier;
                    $definition = self::validateDefinition($packageKey, $serviceType, $identifier, $definition);

                    $merged[$serviceType->value][$identifier] = array_replace(
                        $merged[$serviceType->value][$identifier] ?? [],
                        $definition
                    );
                }
            }
        }

        $result = [];
        foreach ($merged as $serviceTypeValue => $statuses) {
            $result[$serviceTypeValue] = [];
            foreach ($this->order($serviceTypeValue, $statuses) as $identifier => $definition) {
                if (!isset($definition['label'])) {
                    throw new UnexpectedValueException(
                        'The ' . $serviceTypeValue . ' status "' . $identifier . '" has no label.',
                        1795068609
                    );
                }
                $result[$serviceTypeValue][(string) $identifier] = $definition['label'];
            }
        }

        return $result;
    }

    /**
     * @return array{label?: string, before?: list<string>, after?: list<string>}
     */
    private static function validateDefinition(string $packageKey, ServiceType $serviceType, string $identifier, mixed $definition): array
    {
        $location = 'The ' . $serviceType->value . ' status "' . $identifier . '" of the Configuration/OrderStatus.php of the package "' . $packageKey . '"';

        if (preg_match(self::IDENTIFIER_PATTERN, $identifier) !== 1) {
            throw new UnexpectedValueException(
                $location . ' has an invalid identifier, it must match ' . self::IDENTIFIER_PATTERN . '.',
                1795068610
            );
        }
        if (!is_array($definition)) {
            throw new UnexpectedValueException(
                $location . ' must be an array.',
                1795068611
            );
        }

        $unknownKeys = array_diff(array_map(strval(...), array_keys($definition)), self::ALLOWED_KEYS);
        if ($unknownKeys !== []) {
            throw new UnexpectedValueException(
                $location . ' contains the unknown keys "' . implode('", "', $unknownKeys) . '", allowed are "' . implode('", "', self::ALLOWED_KEYS) . '".',
                1795068612
            );
        }

        $validDefinition = [];
        if (array_key_exists('label', $definition)) {
            if (!is_string($definition['label']) || $definition['label'] === '') {
                throw new UnexpectedValueException(
                    $location . ' must have a label of type non-empty string.',
                    1795068613
                );
            }
            $validDefinition['label'] = $definition['label'];
        }

        foreach (['before', 'after'] as $relation) {
            if (!array_key_exists($relation, $definition)) {
                continue;
            }
            $references = $definition[$relation];
            if (!is_array($references) || !array_is_list($references) || array_filter($references, is_string(...)) !== $references) {
                throw new UnexpectedValueException(
                    $location . ' must have a list of status identifiers in "' . $relation . '".',
                    1795068614
                );
            }
            $validDefinition[$relation] = $references;
        }

        return $validDefinition;
    }

    /**
     * @param array<string, array{label?: string, before?: list<string>, after?: list<string>}> $statuses
     *
     * @return array<string, array{label?: string, before?: list<string>, after?: list<string>}>
     */
    private function order(string $serviceTypeValue, array $statuses): array
    {
        try {
            $orderedIdentifiers = array_keys($this->dependencyOrderingService->orderByDependencies($statuses));
        } catch (UnexpectedValueException $exception) {
            throw new UnexpectedValueException(
                'The ' . $serviceTypeValue . ' status cannot be ordered: ' . $exception->getMessage(),
                1795068615,
                $exception
            );
        }

        $orderedStatuses = [];
        foreach ($orderedIdentifiers as $identifier) {
            $orderedStatuses[(string) $identifier] = $statuses[$identifier];
        }

        return $orderedStatuses;
    }
}
