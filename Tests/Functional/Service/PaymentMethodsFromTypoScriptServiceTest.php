<?php

namespace Extcode\Cart\Tests\Functional\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\ServiceFactory;
use Extcode\Cart\Service\PaymentMethodsFromTypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class PaymentMethodsFromTypoScriptServiceTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        $this->testExtensionsToLoad[] = 'typo3conf/ext/cart';

        parent::setUp();
    }

    /**
     * @test
     */
    public function getTypePluginSettingsReturnsTypeCountrySettings()
    {
        $type = 'payments';
        $country = 'de';

        $configurations = [
            $type => [
                'countries' => [
                    $country => [
                        'preset' => 1,
                        'options' => [
                            '1' => [
                                'title' => 'Payment 1 DE',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                            '2' => [
                                'title' => 'Payment 2 DE',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                            '3' => [
                                'title' => 'Payment 3 DE',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                        ],
                    ],
                    'at' => [
                        'preset' => 1,
                        'options' => [
                            '1' => [
                                'title' => 'Payment 1 AT',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                            '2' => [
                                'title' => 'Payment 2 AT',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                            '3' => [
                                'title' => 'Payment 3 AT',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);

        $paymentMethodsService = new PaymentMethodsFromTypoScriptService($configurationManager, new ServiceFactory());

        $reflection = new \ReflectionClass($paymentMethodsService);
        $reflection_property = $reflection->getProperty('configurations');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($paymentMethodsService, $configurations);

        $parsedData = $paymentMethodsService->getConfigurationsForType($type, $country);

        self::assertIsArray(
            $parsedData
        );

        self::assertEquals(
            2,
            count($parsedData)
        );

        self::assertEquals(
            $configurations[$type]['countries'][$country]['options'],
            $parsedData['options']
        );
    }

    /**
     * @test
     */
    public function getTypeZonePluginSettingsReturnsTypeZoneSettings()
    {
        $type = 'payments';
        $country = 'at';

        $configurations = [
            $type => [
                'default' => 'de',
                'zones' => [
                    '1' => [
                        'preset' => 1,
                        'countries' => 'de',
                        'options' => [
                            '1' => [
                                'title' => 'Payment 1 Zone 1',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                            '2' => [
                                'title' => 'Payment 2 Zone 1',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                            '3' => [
                                'title' => 'Payment 3 Zone 1',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                        ],
                    ],
                    '2' => [
                        'preset' => 1,
                        'countries' => $country . ', ch',
                        'options' => [
                            '1' => [
                                'title' => 'Payment 1 Zone 2',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                            '2' => [
                                'title' => 'Payment 2 Zone 2',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                            '3' => [
                                'title' => 'Payment 3 Zone 2',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);

        $paymentMethodsService = new PaymentMethodsFromTypoScriptService($configurationManager, new ServiceFactory());

        $reflection = new \ReflectionClass($paymentMethodsService);
        $reflection_property = $reflection->getProperty('configurations');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($paymentMethodsService, $configurations);

        $parsedData = $paymentMethodsService->getConfigurationsForType($type, $country);

        self::assertIsArray(
            $parsedData
        );

        self::assertEquals(
            3,
            count($parsedData)
        );

        self::assertEquals(
            $configurations[$type]['zones']['2']['options'],
            $parsedData['options']
        );
    }
}
