<?php

declare(strict_types=1);

namespace Extcode\Cart\OrderStatus\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\OrderStatus\ServiceType;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\NoServerRequestGivenException;

/**
 * Reads the email rules of plugin.tx_cart.statusChange.mail from the
 * TypoScript of the current request, e.g. of the storage folder of the order
 * in the backend module.
 */
final readonly class StatusChangeMailRuleLoader
{
    public function __construct(
        private ConfigurationManagerInterface $configurationManager,
        private TypoScriptService $typoScriptService,
    ) {
    }

    /**
     * @return null|array<array-key, mixed> plugin.tx_cart as plain array, null if no TypoScript is available, e.g. on the command line
     */
    public function loadPluginTypoScript(): ?array
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if ($request instanceof ServerRequestInterface
            && ApplicationType::fromRequest($request)->isFrontend()
            && !self::hasFrontendTypoScript($request)
        ) {
            // e.g. a notification of a payment provider handled by a middleware
            return null;
        }

        try {
            $setup = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        } catch (NoServerRequestGivenException) {
            return null;
        }

        $pluginSetup = ArrayUtility::isValidPath($setup, 'plugin./tx_cart.') ? ArrayUtility::getValueByPath($setup, 'plugin./tx_cart.') : [];

        return is_array($pluginSetup) ? $this->typoScriptService->convertTypoScriptArrayToPlainArray($pluginSetup) : [];
    }

    /**
     * @param array<array-key, mixed> $pluginTypoScript plugin.tx_cart as plain array
     *
     * @return list<InvalidStatusChangeMailRule|StatusChangeMailRule> the rules of plugin.tx_cart.statusChange.mail.<serviceType>
     */
    public function getRules(array $pluginTypoScript, ServiceType $serviceType): array
    {
        $path = 'statusChange/mail/' . $serviceType->value;
        $configurations = ArrayUtility::isValidPath($pluginTypoScript, $path) ? ArrayUtility::getValueByPath($pluginTypoScript, $path) : [];
        if (!is_array($configurations)) {
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

    private static function hasFrontendTypoScript(ServerRequestInterface $request): bool
    {
        $frontendTypoScript = $request->getAttribute('frontend.typoscript');

        return $frontendTypoScript instanceof FrontendTypoScript && $frontendTypoScript->hasSetup();
    }
}
