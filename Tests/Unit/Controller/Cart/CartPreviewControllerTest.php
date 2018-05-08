<?php

namespace Extcode\Cart\Tests\Controller\Cart;

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
 * CartPreview Controller Test
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CartPreviewControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $mockedObjectManager;

    /**
     * @var \Extcode\Cart\Controller\Cart\CartPreviewController
     */
    protected $subject = null;

    /**
     * @var \TYPO3\CMS\Extbase\Mvc\View\ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $view = null;

    /**
     * @var \Extcode\Cart\Utility\CartUtility
     */
    protected $cartUtility;

    /**
     * Set Up
     */
    protected function setUp()
    {
        $this->setUpConfiguration();

        $this->subject = $this->getAccessibleMock(
            \Extcode\Cart\Controller\Cart\CartPreviewController::class,
            ['parseData']
        );
        $this->subject
            ->expects($this->any())
            ->method('parseData')
            ->will($this->returnValue(void));

        $this->setUpSettings();

        $this->mockedObjectManager = $this->getMock(
            \TYPO3\CMS\Extbase\Object\ObjectManagerInterface::class
        );
        $this->inject($this->subject, 'objectManager', $this->mockedObjectManager);

        $this->view = $this->getMock(
            \TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class
        );
        $this->inject($this->subject, 'view', $this->view);

        $sessionHandler = $this->getMock(
            \Extcode\Cart\Service\SessionHandler::class,
            [],
            [],
            '',
            false
        );
        $this->inject($this->subject, 'sessionHandler', $sessionHandler);

        $parserUtility = $this->getParserUtility();
        $this->inject($this->subject, 'parserUtility', $parserUtility);

        $cartUtility = $this->getCartUtility();
        $this->inject($cartUtility, 'parserUtility', $parserUtility);
        $this->inject($this->subject, 'cartUtility', $cartUtility);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getParserUtility()
    {
        $parserUtility = $this->getMock(
            \Extcode\Cart\Utility\ParserUtility::class,
            ['parseTaxClasses'],
            [],
            '',
            false
        );
        $parserUtility
            ->expects($this->any())
            ->method('parseTaxClasses')
            ->will($this->returnValue([]));
        return $parserUtility;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCartUtility()
    {
        $cartUtility = $this->getMock(
            \Extcode\Cart\Utility\CartUtility::class,
            ['parseServices'],
            [],
            '',
            false
        );
        $cartUtility
            ->expects($this->any())
            ->method('parseServices')
            ->will($this->returnValue([]));

        $mockedObjectManager = clone $this->mockedObjectManager;
        $mockedObjectManager->method('get')->willReturn(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Extcode\Cart\Domain\Model\Cart\Cart::class, []));
        $this->inject($cartUtility, 'objectManager', $mockedObjectManager);

        return $cartUtility;
    }

    /**
     * Set Up Configuration
     */
    protected function setUpConfiguration()
    {
        $configuration = [];
        $configurationManager = $this->getMock(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::class
        );
        $configurationManager
            ->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration));
    }

    /**
     * Set Up Settings
     */
    protected function setUpSettings()
    {
        $settings = [
            'cart' => [
                'pid' => 1
            ]
        ];
        $this->subject->_set('settings', $settings);

        $pluginSettings = [];
        $this->subject->_set('pluginSettings', $pluginSettings);
    }

    /**
     * @test
     */
    public function showActionCanBeCalled()
    {
        $this->subject->showAction();
    }

    /**
     * @test
     */
    public function showPassesCartToView()
    {
        $cart = new \Extcode\Cart\Domain\Model\Cart\Cart([], false, 'EUR', '€', 1.0);

        $this->view->expects(self::once())->method('assign')->with('cart', $cart);

        $this->subject->showAction();
    }
}
