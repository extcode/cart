<?php

declare(strict_types=1);

namespace Extcode\Cart\OrderStatus\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\OrderStatus\ServiceType;
use Extcode\Cart\Utility\ArrayUtility;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Reads the email rules of the site settings extcode.cart.statusChange.mail
 * from the site of the storage page of the order, so it works without request
 * too, e.g. on the command line.
 */
final readonly class StatusChangeMailRuleLoader
{
    public function __construct(
        private SiteFinder $siteFinder,
    ) {
    }

    /**
     * @return null|list<InvalidStatusChangeMailRule|StatusChangeMailRule> the rules of
     *                                                                     extcode.cart.statusChange.mail.<serviceType>, null if
     *                                                                     the storage page of the order is in no site
     */
    public function getRules(Item $orderItem, ServiceType $serviceType): ?array
    {
        try {
            $settings = $this->siteFinder->getSiteByPageId((int) $orderItem->getPid())->getSettings()->getAll();
        } catch (SiteNotFoundException) {
            return null;
        }

        $path = 'extcode.cart.statusChange.mail.' . $serviceType->value;
        $configurations = ArrayUtility::getArrayByPathWithFallback($settings, $path, '.');
        if ($configurations === null) {
            return [];
        }

        $result = [];
        foreach ($configurations as $key => $configuration) {
            $key = (string) $key;
            if (!is_array($configuration)) {
                $result[] = new InvalidStatusChangeMailRule($key, 'The configuration must be an array.');
                continue;
            }
            try {
                $result[] = StatusChangeMailRule::fromArray($key, $serviceType, $configuration);
            } catch (InvalidMailConfigurationException $exception) {
                $result[] = new InvalidStatusChangeMailRule($key, $exception->getMessage());
            }
        }

        return $result;
    }
}
