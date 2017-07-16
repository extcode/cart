<?php

namespace Extcode\Cart\Tests\Functional\Utility;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Product Product Repository
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ParserUtilityTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Extcode\Cart\Utility\ParserUtility
     */
    protected $parserUtility;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/cart',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Object\ObjectManager::class
        );

        $this->parserUtility = $this->objectManager->get(
            \Extcode\Cart\Utility\ParserUtility::class
        );
    }

    /**
     * @test
     */
    public function parsingTaxClassesFromTypoScriptWithoutCountryCodeReturnsDirectlyConfiguredArrayOfTaxClasses()
    {
        $pluginSettings = [
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

        $taxClasses = $this->parserUtility->parseTaxClasses($pluginSettings, $countryCode);

        $this->assertInternalType(
            'array',
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
            $pluginSettings['taxClasses']['1']['name'],
            $firstTaxClasses->getTitle()
        );
    }

    /**
     * @test
     */
    public function parsingTaxClassesFromTypoScriptWithCountryCodeReturnsCountrySpecificArrayOfTaxClasses()
    {
        $pluginSettings = [
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

        $taxClasses = $this->parserUtility->parseTaxClasses($pluginSettings, $countryCode);

        $this->assertInternalType(
            'array',
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
            $pluginSettings['taxClasses']['AT']['1']['name'],
            $firstTaxClasses->getTitle()
        );
    }
}
