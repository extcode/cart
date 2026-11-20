<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Loader\SiteSets;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\MailConfigurationLoaderInterface;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Mail\BuyerMailConfiguration;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\Mail\SellerMailConfiguration;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\SiteSettings;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Reads the settings extcode.cart.mail.buyer.* and extcode.cart.mail.seller.* of the
 * site set extcode/cart-mail from the site of the storage page of the order,
 * so it works without request too, e.g. on the command line.
 */
final readonly class MailConfigurationLoader implements MailConfigurationLoaderInterface
{
    private const PREFIX = 'extcode.cart.mail.';

    public function __construct(
        private SiteFinder $siteFinder,
    ) {
    }

    public function getBuyerMailConfiguration(string $template, Item $orderItem): BuyerMailConfiguration
    {
        $settings = $this->getSiteSettings($orderItem);

        return new BuyerMailConfiguration(
            $template,
            self::getString($settings, 'buyer.fromAddress'),
            self::getString($settings, 'buyer.fromName'),
            self::getStringList($settings, 'buyer.ccAddress'),
            self::getStringList($settings, 'buyer.bccAddress'),
            self::getStringList($settings, 'buyer.replyToAddress'),
        );
    }

    public function getSellerMailConfiguration(string $template, Item $orderItem): SellerMailConfiguration
    {
        $settings = $this->getSiteSettings($orderItem);

        $to = self::getStringList($settings, 'seller.toAddress');
        if ($to === []) {
            throw new InvalidMailConfigurationException(
                'The setting "' . self::PREFIX . 'seller.toAddress" of the site is missing.',
                1795150002
            );
        }

        return new SellerMailConfiguration(
            $template,
            $to,
            self::getString($settings, 'seller.fromAddress'),
            self::getString($settings, 'seller.fromName'),
            self::getStringList($settings, 'seller.ccAddress'),
            self::getStringList($settings, 'seller.bccAddress'),
        );
    }

    /**
     * @throws InvalidMailConfigurationException if the storage page of the order is in no site
     */
    private function getSiteSettings(Item $orderItem): SiteSettings
    {
        $pageId = (int) $orderItem->getPid();
        try {
            return $this->siteFinder->getSiteByPageId($pageId)->getSettings();
        } catch (SiteNotFoundException $exception) {
            throw new InvalidMailConfigurationException(
                'The storage page ' . $pageId . ' of the order is in no site.',
                1795150001,
                $exception
            );
        }
    }

    private static function getString(SiteSettings $settings, string $key): string
    {
        $value = $settings->get(self::PREFIX . $key, '');

        return is_scalar($value) ? trim((string) $value) : '';
    }

    /**
     * @return list<string> the non-empty strings of the setting of type stringlist
     */
    private static function getStringList(SiteSettings $settings, string $key): array
    {
        $value = $settings->get(self::PREFIX . $key, []);
        if (!is_array($value)) {
            return [];
        }

        $strings = [];
        foreach ($value as $string) {
            $string = is_scalar($string) ? trim((string) $string) : '';
            if ($string !== '') {
                $strings[] = $string;
            }
        }

        return $strings;
    }
}
