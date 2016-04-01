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
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CartControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
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

        $this->view = $this->getMock(
            \TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class
        );
        $this->inject($this->subject, 'view', $this->view);

        $this->couponRepository = $this->getMock(
            \Extcode\Cart\Domain\Repository\Product\CouponRepository::class,
            array(),
            array(),
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
            array('parseServices'),
            array(),
            '',
            false
        );
        $cartUtility
            ->expects($this->any())
            ->method('parseServices')
            ->will($this->returnValue([]));

        $sessionHandler = $this->getMock(
            \Extcode\Cart\Service\SessionHandler::class,
            array(),
            array(),
            '',
            false
        );
        $this->inject($cartUtility, 'sessionHandler', $sessionHandler);

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
        $assignArguments = array(
            'shippings' => [],
            'payments' => [],
            'specials' => []
        );

        $this->view->expects(self::at(1))->method('assignMultiple')->with($assignArguments);

        $this->subject->showCartAction();
    }

    /**
     * @test
     */
    public function showCartPassesNewOrderItemAndNewAddressesToView()
    {
        $assignArguments = array(
            'orderItem' => new \Extcode\Cart\Domain\Model\Order\Item(),
            'billingAddress' => new \Extcode\Cart\Domain\Model\Order\Address(),
            'shippingAddress' => new \Extcode\Cart\Domain\Model\Order\Address()
        );

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
            array(),
            array(),
            '',
            false
        );

        $assignArguments = array(
            'orderItem' => $orderItem,
            'billingAddress' => new \Extcode\Cart\Domain\Model\Order\Address(),
            'shippingAddress' => new \Extcode\Cart\Domain\Model\Order\Address()
        );

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
            array(),
            array(),
            '',
            false
        );

        $assignArguments = array(
            'orderItem' => new \Extcode\Cart\Domain\Model\Order\Item(),
            'billingAddress' => $billingAddress,
            'shippingAddress' => new \Extcode\Cart\Domain\Model\Order\Address()
        );

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
            array(),
            array(),
            '',
            false
        );

        $assignArguments = array(
            'orderItem' => new \Extcode\Cart\Domain\Model\Order\Item(),
            'billingAddress' => new \Extcode\Cart\Domain\Model\Order\Address(),
            'shippingAddress' => $shippingAddress
        );

        $this->view->expects(self::at(2))->method('assignMultiple')->with($assignArguments);

        $this->subject->showCartAction(null, null, $shippingAddress);
    }

    /**
     * @test
     */
    public function showMiniActionCanBeCalled()
    {
        $this->subject->showMiniAction();
    }

    /**
     * @test
     */
    public function showMiniPassesCartToView()
    {
        $cart = new \Extcode\Cart\Domain\Model\Cart\Cart([], 0);

        $this->view->expects(self::once())->method('assign')->with('cart', $cart);

        $this->subject->showMiniAction();
    }
}
