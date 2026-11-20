<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Loader\TypoScript;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\MailConfigurationLoaderInterface;
use Extcode\Cart\Mail\BuyerMailConfiguration;
use Extcode\Cart\Mail\SellerMailConfiguration;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Reads plugin.tx_cart.mail.buyer and plugin.tx_cart.mail.seller. The
 * settings of the plugin (FlexForm), e.g. settings.buyer.emailFromAddress,
 * override them.
 */
final readonly class MailConfigurationLoader implements MailConfigurationLoaderInterface
{
    /**
     * The keys of plugin.tx_cart.mail.<recipient> and of settings.<recipient>.
     */
    private const SETTINGS_KEYS = [
        'fromName' => 'emailFromName',
        'fromAddress' => 'emailFromAddress',
        'toAddress' => 'emailToAddress',
        'ccAddress' => 'emailCcAddress',
        'bccAddress' => 'emailBccAddress',
        'replyToAddress' => 'emailReplyToAddress',
    ];

    public function __construct(
        private ConfigurationManagerInterface $configurationManager,
    ) {
    }

    public function getBuyerMailConfiguration(string $template): BuyerMailConfiguration
    {
        return BuyerMailConfiguration::fromArray($template, $this->getSettings('buyer'));
    }

    public function getSellerMailConfiguration(string $template): SellerMailConfiguration
    {
        return SellerMailConfiguration::fromArray($template, $this->getSettings('seller'));
    }

    /**
     * @return array<string, string> the keys of plugin.tx_cart.mail.<recipient>
     */
    private function getSettings(string $recipient): array
    {
        $configuration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
        $mailSettings = self::getArray(self::getArray($configuration, 'mail'), $recipient);
        $pluginSettings = self::getArray(self::getArray($configuration, 'settings'), $recipient);

        $settings = [];
        foreach (self::SETTINGS_KEYS as $key => $pluginKey) {
            $value = self::getString($pluginSettings, $pluginKey);
            if ($value === '') {
                $value = self::getString($mailSettings, $key);
            }
            if ($value !== '') {
                $settings[$key] = $value;
            }
        }

        return $settings;
    }

    /**
     * @param array<array-key, mixed> $settings
     *
     * @return array<array-key, mixed>
     */
    private static function getArray(array $settings, string $key): array
    {
        $value = $settings[$key] ?? [];

        return is_array($value) ? $value : [];
    }

    /**
     * @param array<array-key, mixed> $settings
     */
    private static function getString(array $settings, string $key): string
    {
        $value = $settings[$key] ?? '';

        return is_scalar($value) ? trim((string) $value) : '';
    }
}
