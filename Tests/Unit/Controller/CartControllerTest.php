<?php

namespace Extcode\Cart\Tests\Controller;

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
 * Cart Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CartControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $mockedObjectManager;

    /**
     * @var \Extcode\Cart\Controller\CartController
     */
    protected $subject = null;

    /**
     * @var \TYPO3\CMS\Extbase\Mvc\View\ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $view = null;

    /**
     * @var \Extcode\Cart\Domain\Repository\Product\CouponRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $couponRepository = null;

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
            \Extcode\Cart\Controller\CartController::class,
            ['dummy']
        );

        $this->setUpSettings();

        $this->mockedObjectManager = $this->getMock(
            \TYPO3\CMS\Extbase\Object\ObjectManagerInterface::class
        );
        $this->inject($this->subject, 'objectManager', $this->mockedObjectManager);

        $this->view = $this->getMock(
            \TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class
        );
        $this->inject($this->subject, 'view', $this->view);

        $this->couponRepository = $this->getMock(
            \Extcode\Cart\Domain\Repository\Product\CouponRepository::class,
            [],
            [],
            '',
            false
        );
        $this->inject($this->subject, 'couponRepository', $this->couponRepository);

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

        $sessionHandler = $this->getMock(
            \Extcode\Cart\Service\SessionHandler::class,
            [],
            [],
            '',
            false
        );
        $this->inject($cartUtility, 'sessionHandler', $sessionHandler);

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
    public function showCartActionCanBeCalled()
    {
        $this->subject->showCartAction();
    }

    /**
     * @test
     */
    public function showCartPassesCartToView()
    {
        $cart = new \Extcode\Cart\Domain\Model\Cart\Cart([], 0);

        $this->view->expects(self::once())->method('assign')->with('cart', $cart);

        $this->subject->showCartAction();
    }

    /**
     * @test
     */
    public function showCartPassesShippingsPaymentsSpecialsToView()
    {
        $assignArguments = [
            'shippings' => [],
            'payments' => [],
            'specials' => []
        ];

        $this->view->expects(self::at(1))->method('assignMultiple')->with($assignArguments);

        $this->subject->showCartAction();
    }

    /**
     * @test
     */
    public function showCartPassesNewOrderItemAndNewAddressesToView()
    {
        $assignArguments = [
            'orderItem' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\Item::class
            ),
            'billingAddress' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\Address::class
            ),
            'shippingAddress' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\Address::class
            ),
        ];

        $this->view->expects(self::at(2))->method('assignMultiple')->with($assignArguments);

        $this->subject->showCartAction();
    }

    /**
     * @test
     */
    public function showCartPassesOrderItemToView()
    {
        $orderItem = $this->getMock(
            \Extcode\Cart\Domain\Model\Order\Item::class,
            [],
            [],
            '',
            false
        );

        $assignArguments = [
            'orderItem' => $orderItem,
            'billingAddress' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\Address::class
            ),
            'shippingAddress' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\Address::class
            ),
        ];

        $this->view->expects(self::at(2))->method('assignMultiple')->with($assignArguments);

        $this->subject->showCartAction($orderItem, null, null);
    }

    /**
     * @test
     */
    public function showCartPassesBillingAddressItemToView()
    {
        $billingAddress = $this->getMock(
            \Extcode\Cart\Domain\Model\Order\Address::class,
            [],
            [],
            '',
            false
        );

        $assignArguments = [
            'orderItem' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\Item::class
            ),
            'billingAddress' => $billingAddress,
            'shippingAddress' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\Address::class
            ),
        ];

        $this->view->expects(self::at(2))->method('assignMultiple')->with($assignArguments);

        $this->subject->showCartAction(null, $billingAddress, null);
    }

    /**
     * @test
     */
    public function showCartPassesShippingAddressItemToView()
    {
        $shippingAddress = $this->getMock(
            \Extcode\Cart\Domain\Model\Order\Address::class,
            [],
            [],
            '',
            false
        );

        $assignArguments = [
            'orderItem' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\Item::class
            ),
            'billingAddress' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\Address::class
            ),
            'shippingAddress' => $shippingAddress
        ];

        $this->view->expects(self::at(2))->method('assignMultiple')->with($assignArguments);

        $this->subject->showCartAction(null, null, $shippingAddress);
    }

    /**
     * @test
     */
    public function showMiniCartActionCanBeCalled()
    {
        $this->subject->showMiniCartAction();
    }

    /**
     * @test
     */
    public function showMiniCartPassesCartToView()
    {
        $cart = new \Extcode\Cart\Domain\Model\Cart\Cart([], 0);

        $this->view->expects(self::once())->method('assign')->with('cart', $cart);

        $this->subject->showMiniCartAction();
    }
}
