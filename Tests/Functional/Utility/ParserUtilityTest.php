<?php

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Extcode\Cart\Tests\Functional\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ParserUtilityTest extends FunctionalTestCase
{
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

    public function setUp(): void
    {
        parent::setUp();

        $this->parserUtility = GeneralUtility::makeInstance(
            \Extcode\Cart\Utility\ParserUtility::class
        );
    }

    /**
     * @test
     */
    public function getTypePluginSettingsReturnsTypeCountrySettings()
    {
        $taxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'normal');

        $type = 'payment';
        $country = 'de';

        $pluginSettings = [
            $type => [
                'default' => 'de',
                'de' => [
                    'preset' => 1,
                    'options' => [
                        '1' => [
                            'title' => 'Payment 1 DE',
                            'extra' => '0.00',
                            'taxClassId' => '1',
                            'status' => 'open'
                        ],
                        '2' => [
                            'title' => 'Payment 2 DE',
                            'extra' => '0.00',
                            'taxClassId' => '1',
                            'status' => 'open'
                        ],
                        '3' => [
                            'title' => 'Payment 3 DE',
                            'extra' => '0.00',
                            'taxClassId' => '1',
                            'status' => 'open'
                        ]
                    ]
                ],
                'at' => [
                    'preset' => 1,
                    'options' => [
                        '1' => [
                            'title' => 'Payment 1 AT',
                            'extra' => '0.00',
                            'taxClassId' => '1',
                            'status' => 'open'
                        ],
                        '2' => [
                            'title' => 'Payment 2 AT',
                            'extra' => '0.00',
                            'taxClassId' => '1',
                            'status' => 'open'
                        ],
                        '3' => [
                            'title' => 'Payment 3 AT',
                            'extra' => '0.00',
                            'taxClassId' => '1',
                            'status' => 'open'
                        ]
                    ]
                ],
            ],
        ];

        $cart = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getCountry'])
            ->setConstructorArgs([[$taxClass]])
            ->getMock();
        $cart
            ->expects($this->any())
            ->method('getCountry')
            ->will($this->returnValue($country));

        $parsedData = $this->parserUtility->getTypePluginSettings($pluginSettings, $cart, $type);

        $this->assertIsArray(
            $parsedData
        );

        $this->assertEquals(
            2,
            count($parsedData)
        );

        $this->assertEquals(
            $pluginSettings[$type][$country]['options'],
            $parsedData['options']
        );
    }

    /**
     * @test
     */
    public function getTypeZonePluginSettingsReturnsTypeZoneSettings()
    {
        $taxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'normal');

        $type = 'payment';
        $country = 'at';

        $pluginSettings = [
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
                                'status' => 'open'
                            ],
                            '2' => [
                                'title' => 'Payment 2 Zone 1',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open'
                            ],
                            '3' => [
                                'title' => 'Payment 3 Zone 1',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open'
                            ]
                        ]
                    ],
                    '2' => [
                        'preset' => 1,
                        'countries' => 'at, ch',
                        'options' => [
                            '1' => [
                                'title' => 'Payment 1 Zone 2',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open'
                            ],
                            '2' => [
                                'title' => 'Payment 2 Zone 2',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open'
                            ],
                            '3' => [
                                'title' => 'Payment 3 Zone 2',
                                'extra' => '0.00',
                                'taxClassId' => '1',
                                'status' => 'open'
                            ]
                        ]
                    ],
                ],
            ],
        ];

        $cart = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getCountry'])
            ->setConstructorArgs([[$taxClass]])
            ->getMock();
        $cart
            ->expects($this->any())
            ->method('getCountry')
            ->will($this->returnValue($country));

        $parsedData = $this->parserUtility->getTypePluginSettings($pluginSettings, $cart, $type);

        $this->assertIsArray(
            $parsedData
        );

        $this->assertEquals(
            3,
            count($parsedData)
        );

        $this->assertEquals(
            $pluginSettings[$type]['zones']['2']['options'],
            $parsedData['options']
        );
    }
}
