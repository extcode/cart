<?php

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Extcode\Cart\Tests\Functional\Utility;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class TaxClassServiceTest extends FunctionalTestCase
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Extcode\Cart\Service\TaxClassService
     */
    protected $taxClassService;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/cart',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Object\ObjectManager::class
        );

        $this->taxClassService = $this->objectManager->get(
            \Extcode\Cart\Service\TaxClassService::class
        );
    }

    /**
     * @test
     */
    public function parsingTaxClassesFromTypoScriptWithoutCountryCodeReturnsDirectlyConfiguredArrayOfTaxClasses()
    {
        $settings = [
            'taxClasses' => [
                '1' => [
                    'value' => '19',
                    'calc' => '0.19',
                    'name' => 'normal',
                ],
                '2' => [
                    'value' => '7',
                    'calc' => '0.07',
                    'name' => 'reduced',
                ],
                '3' => [
                    'value' => '0',
                    'calc' => '0.00',
                    'name' => 'free',
                ],
            ],
        ];

        $reflection = new \ReflectionClass($this->taxClassService);
        $reflection_property = $reflection->getProperty('settings');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->taxClassService, $settings);

        $countryCode = '';

        $taxClasses = $this->taxClassService->getTaxClasses($countryCode);

        $this->assertIsArray(
            $taxClasses
        );

        $this->assertEquals(
            3,
            count($taxClasses)
        );

        $firstTaxClasses = $taxClasses[1];
        $this->assertInstanceOf(
            \Extcode\Cart\Domain\Model\Cart\TaxClass::class,
            $firstTaxClasses
        );

        $this->assertEquals(
            $settings['taxClasses']['1']['name'],
            $firstTaxClasses->getTitle()
        );
    }

    /**
     * @test
     */
    public function parsingTaxClassesFromTypoScriptWithCountryCodeReturnsCountrySpecificArrayOfTaxClasses()
    {
        $settings = [
            'taxClasses' => [
                'DE' => [
                    '1' => [
                        'value' => '19',
                        'calc' => '0.19',
                        'name' => 'DE normal',
                    ],
                    '2' => [
                        'value' => '7',
                        'calc' => '0.07',
                        'name' => 'DE reduced',
                    ],
                    '3' => [
                        'value' => '0',
                        'calc' => '0.00',
                        'name' => 'DE free',
                    ],
                ],
                'AT' => [
                    '1' => [
                        'value' => '20',
                        'calc' => '0.20',
                        'name' => 'AT normal',
                    ],
                    '2' => [
                        'value' => '10',
                        'calc' => '0.10',
                        'name' => 'AT reduced',
                    ],
                    '3' => [
                        'value' => '0',
                        'calc' => '0.00',
                        'name' => 'AT free',
                    ],
                ],
            ],
        ];

        $reflection = new \ReflectionClass($this->taxClassService);
        $reflection_property = $reflection->getProperty('settings');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->taxClassService, $settings);

        $countryCode = 'AT';

        $taxClasses = $this->taxClassService->getTaxClasses($countryCode);

        $this->assertIsArray(
            $taxClasses
        );

        $this->assertEquals(
            3,
            count($taxClasses)
        );

        $firstTaxClasses = $taxClasses[1];
        $this->assertInstanceOf(
            \Extcode\Cart\Domain\Model\Cart\TaxClass::class,
            $firstTaxClasses
        );

        $this->assertEquals(
            $settings['taxClasses']['AT']['1']['name'],
            $firstTaxClasses->getTitle()
        );
    }

    /**
     * @test
     */
    public function parsingTaxClassesFromTypoScriptWithNotConfiguredCountryCodeReturnsFallbackArrayOfTaxClasses()
    {
        $settings = [
            'taxClasses' => [
                'DE' => [
                    '1' => [
                        'value' => '19',
                        'calc' => '0.19',
                        'name' => 'DE normal',
                    ],
                    '2' => [
                        'value' => '7',
                        'calc' => '0.07',
                        'name' => 'DE reduced',
                    ],
                    '3' => [
                        'value' => '0',
                        'calc' => '0.00',
                        'name' => 'DE free',
                    ],
                ],
                'AT' => [
                    '1' => [
                        'value' => '20',
                        'calc' => '0.20',
                        'name' => 'AT normal',
                    ],
                    '2' => [
                        'value' => '10',
                        'calc' => '0.10',
                        'name' => 'AT reduced',
                    ],
                    '3' => [
                        'value' => '0',
                        'calc' => '0.00',
                        'name' => 'AT free',
                    ],
                ],
                'fallback' => [
                    '1' => [
                        'value' => '0',
                        'calc' => '0.00',
                        'name' => 'other normal',
                    ],
                    '2' => [
                        'value' => '0',
                        'calc' => '0.00',
                        'name' => 'other reduced',
                    ],
                    '3' => [
                        'value' => '0',
                        'calc' => '0.00',
                        'name' => 'other free',
                    ],
                ],
            ],
        ];

        $reflection = new \ReflectionClass($this->taxClassService);
        $reflection_property = $reflection->getProperty('settings');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($this->taxClassService, $settings);

        $countryCode = 'CH';

        $taxClasses = $this->taxClassService->getTaxClasses($countryCode);

        $this->assertIsArray(
            $taxClasses
        );

        $this->assertEquals(
            3,
            count($taxClasses)
        );

        $firstTaxClasses = $taxClasses[1];
        $this->assertInstanceOf(
            \Extcode\Cart\Domain\Model\Cart\TaxClass::class,
            $firstTaxClasses
        );

        $this->assertEquals(
            $settings['taxClasses']['fallback']['1']['name'],
            $firstTaxClasses->getTitle()
        );
    }
}
