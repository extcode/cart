<?php

declare(strict_types=1);

namespace Extcode\Cart\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\NoServerRequestGivenException;

/**
 * Provides the TypoScript configuration of the cart plugin (plugin.tx_cart) for
 * the emails, e.g. the template variable "settings" and the attachments:
 *
 * - in the frontend the configuration of the plugin, including its FlexForm,
 * - in the backend plugin.tx_cart of the page, e.g. the storage folder of the
 *   order in the backend module (the framework configuration of the backend is
 *   module.tx_cart),
 * - nothing without TypoScript, e.g. on the command line or in a middleware of
 *   a payment provider.
 */
final readonly class PluginConfigurationProvider
{
    public function __construct(
        private ConfigurationManagerInterface $configurationManager,
        private TypoScriptService $typoScriptService,
    ) {
    }

    /**
     * @return array<array-key, mixed> plugin.tx_cart.settings, empty without TypoScript
     */
    public function getSettings(): array
    {
        $configuration = $this->getConfiguration();

        return is_array($configuration['settings'] ?? null) ? $configuration['settings'] : [];
    }

    /**
     * @return array<array-key, mixed> plugin.tx_cart as plain array, empty without TypoScript
     */
    public function getConfiguration(): array
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        $applicationType = $request instanceof ServerRequestInterface && is_int($request->getAttribute('applicationType'))
            ? ApplicationType::fromRequest($request)
            : null;

        try {
            if ($applicationType?->isBackend()) {
                return $this->getPluginTypoScript();
            }
            if ($applicationType?->isFrontend() && !self::hasFrontendTypoScript($request)) {
                return [];
            }

            return $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                'Cart'
            );
        } catch (NoServerRequestGivenException) {
            return [];
        }
    }

    /**
     * @return array<array-key, mixed>
     */
    private function getPluginTypoScript(): array
    {
        $setup = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $pluginSetup = ArrayUtility::isValidPath($setup, 'plugin./tx_cart.') ? ArrayUtility::getValueByPath($setup, 'plugin./tx_cart.') : [];

        return is_array($pluginSetup) ? $this->typoScriptService->convertTypoScriptArrayToPlainArray($pluginSetup) : [];
    }

    private static function hasFrontendTypoScript(?ServerRequestInterface $request): bool
    {
        $frontendTypoScript = $request?->getAttribute('frontend.typoscript');

        return $frontendTypoScript instanceof FrontendTypoScript && $frontendTypoScript->hasSetup();
    }
}
