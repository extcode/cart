<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\OrderStatus;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Status\ServiceType;
use Extcode\Cart\Domain\Model\Order\Status\Status;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Package\PackageManager;

/**
 * The payment and shipping status registered in Configuration/OrderStatus.php
 * of all active packages, see Documentation/Developer/OrderStatus/Index.rst.
 */
final class OrderStatusRegistry
{
    private const CONFIGURATION_FILE = 'Configuration/OrderStatus.php';

    private const CART_PACKAGE_KEY = 'cart';

    /**
     * @var null|array<value-of<ServiceType>, array<string, string>>
     */
    private ?array $configuration = null;

    public function __construct(
        private readonly PackageManager $packageManager,
        private readonly OrderStatusConfigurationBuilder $configurationBuilder,
        #[Autowire(service: 'cache.core')]
        private readonly PhpFrontend $cache,
        #[Autowire(expression: 'service("package-dependent-cache-identifier").withPrefix("CartOrderStatus").toString()')]
        private readonly string $cacheIdentifier,
    ) {
    }

    /**
     * @return array<string, Status> the status by identifier in their order
     */
    public function getStatuses(ServiceType $serviceType): array
    {
        $statuses = [];
        foreach ($this->getConfiguration()[$serviceType->value] ?? [] as $identifier => $label) {
            $statuses[$identifier] = new Status($serviceType, $identifier, $label);
        }

        return $statuses;
    }

    /**
     * A current status which is not registered (anymore) is added at the end
     * with its identifier as label, otherwise a select with these options
     * would change the status of the order unnoticed.
     *
     * @return array<string, string> the localized labels by identifier in the order of the status, e.g. for the options of a select
     */
    public function getLocalizedLabels(ServiceType $serviceType, ?string $currentStatus = null): array
    {
        $labels = array_map(
            static fn (Status $status): string => $status->getLocalizedLabel(),
            $this->getStatuses($serviceType)
        );

        if ($currentStatus !== null && $currentStatus !== '' && !isset($labels[$currentStatus])) {
            $labels[$currentStatus] = $currentStatus;
        }

        return $labels;
    }

    public function hasStatus(ServiceType $serviceType, string $identifier): bool
    {
        return isset($this->getConfiguration()[$serviceType->value][$identifier]);
    }

    public function getStatus(ServiceType $serviceType, string $identifier): ?Status
    {
        $label = $this->getConfiguration()[$serviceType->value][$identifier] ?? null;
        if ($label === null) {
            return null;
        }

        return new Status($serviceType, $identifier, $label);
    }

    /**
     * @return array<value-of<ServiceType>, array<string, string>>
     */
    private function getConfiguration(): array
    {
        if ($this->configuration !== null) {
            return $this->configuration;
        }

        /** @var array<value-of<ServiceType>, array<string, string>>|false $cached */
        $cached = $this->cache->require($this->cacheIdentifier);
        if (is_array($cached)) {
            return $this->configuration = $cached;
        }

        $configuration = $this->configurationBuilder->build($this->loadConfigurationFiles());
        $this->cache->set($this->cacheIdentifier, 'return ' . var_export($configuration, true) . ';');

        return $this->configuration = $configuration;
    }

    /**
     * The status of cart are the base which other packages extend or change,
     * so cart is always loaded first, the other packages in their order.
     *
     * @return array<string, mixed> the return values of the configuration files by package key
     */
    private function loadConfigurationFiles(): array
    {
        $configurations = [
            self::CART_PACKAGE_KEY => require $this->packageManager->getPackage(self::CART_PACKAGE_KEY)->getPackagePath() . self::CONFIGURATION_FILE,
        ];
        foreach ($this->packageManager->getActivePackages() as $package) {
            $file = $package->getPackagePath() . self::CONFIGURATION_FILE;
            if ($package->getPackageKey() !== self::CART_PACKAGE_KEY && is_file($file)) {
                $configurations[$package->getPackageKey()] = require $file;
            }
        }

        return $configurations;
    }
}
