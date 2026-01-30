<?php

namespace Extcode\Cart\Tests\Unit\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\TaxClass;
use Extcode\Cart\Domain\Model\Cart\TaxClassFactory;
use Extcode\Cart\Service\TaxClassService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(TaxClassService::class)]
class TaxClassServiceTest extends UnitTestCase
{
    #[Test]
    public function parsingTaxClassesFromTypoScriptWithoutCountryCodeReturnsDirectlyConfiguredArrayOfTaxClasses(): void
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

        $countryCode = '';

        $taxClasses = $this->createSubject($settings)->getTaxClasses($countryCode);

        self::assertEquals(
            3,
            count($taxClasses)
        );

        $firstTaxClasses = $taxClasses[1];

        // @phpstan-ignore-next-line staticMethod.alreadyNarrowedType
        self::assertInstanceOf(
            TaxClass::class,
            $firstTaxClasses
        );

        self::assertEquals(
            $settings['taxClasses']['1']['name'],
            $firstTaxClasses->getTitle()
        );
    }

    #[Test]
    public function parsingTaxClassesFromTypoScriptWithCountryCodeReturnsCountrySpecificArrayOfTaxClasses(): void
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

        $countryCode = 'AT';

        $taxClasses = $this->createSubject($settings)->getTaxClasses($countryCode);

        self::assertEquals(
            3,
            count($taxClasses)
        );

        $firstTaxClasses = $taxClasses[1];

        // @phpstan-ignore-next-line staticMethod.alreadyNarrowedType
        self::assertInstanceOf(
            TaxClass::class,
            $firstTaxClasses
        );

        self::assertEquals(
            $settings['taxClasses']['AT']['1']['name'],
            $firstTaxClasses->getTitle()
        );
    }

    #[Test]
    public function parsingTaxClassesFromTypoScriptWithNotConfiguredCountryCodeReturnsFallbackArrayOfTaxClasses(): void
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

        $countryCode = 'CH';

        $taxClasses = $this->createSubject($settings)->getTaxClasses($countryCode);

        self::assertEquals(
            3,
            count($taxClasses)
        );

        $firstTaxClasses = $taxClasses[1];

        // @phpstan-ignore-next-line staticMethod.alreadyNarrowedType
        self::assertInstanceOf(
            TaxClass::class,
            $firstTaxClasses
        );

        self::assertEquals(
            $settings['taxClasses']['fallback']['1']['name'],
            $firstTaxClasses->getTitle()
        );
    }

    #[Test]
    public function parsingTaxClassesFromTypoScriptWithIntegerZeroAsCalcIsValid(): void
    {
        $settings = [
            'taxClasses' => [
                '1' => [
                    'value' => '0',
                    'calc' => '0',
                    'name' => 'free',
                ],
            ],
        ];

        $taxClasses = $this->createSubject($settings)->getTaxClasses();

        self::assertEquals(
            $taxClasses[1]->getCalc(),
            0
        );
    }

    private function createSubject(array $settings): TaxClassService
    {
        $configurationManager = self::createStub(ConfigurationManagerInterface::class);
        $configurationManager->method('getConfiguration')->willReturn($settings);

        return new TaxClassService(
            $configurationManager,
            new TaxClassFactory(
                self::createStub(LoggerInterface::class)
            )
        );
    }
}
