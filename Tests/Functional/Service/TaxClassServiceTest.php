<?php

namespace Extcode\Cart\Tests\Functional\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\TaxClass;
use Extcode\Cart\Service\TaxClassService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class TaxClassServiceTest extends FunctionalTestCase
{
    /**
     * @var TaxClassService
     */
    protected $taxClassService;

    /**
     * @var non-empty-string[]
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/cart',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->taxClassService = GeneralUtility::makeInstance(
            TaxClassService::class
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

        self::assertIsArray(
            $taxClasses
        );

        self::assertEquals(
            3,
            count($taxClasses)
        );

        $firstTaxClasses = $taxClasses[1];
        self::assertInstanceOf(
            TaxClass::class,
            $firstTaxClasses
        );

        self::assertEquals(
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

        self::assertIsArray(
            $taxClasses
        );

        self::assertEquals(
            3,
            count($taxClasses)
        );

        $firstTaxClasses = $taxClasses[1];
        self::assertInstanceOf(
            TaxClass::class,
            $firstTaxClasses
        );

        self::assertEquals(
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

        self::assertIsArray(
            $taxClasses
        );

        self::assertEquals(
            3,
            count($taxClasses)
        );

        $firstTaxClasses = $taxClasses[1];
        self::assertInstanceOf(
            TaxClass::class,
            $firstTaxClasses
        );

        self::assertEquals(
            $settings['taxClasses']['fallback']['1']['name'],
            $firstTaxClasses->getTitle()
        );
    }
}
