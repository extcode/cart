<?php

namespace Extcode\Cart\Tests\Unit\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;

class CartControllerTest extends UnitTestCase
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $mockedObjectManager;

    /**
     * @var \Extcode\Cart\Controller\Cart\CartController
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
            \Extcode\Cart\Controller\Cart\CartController::class,
            ['parseData']
        );
        $this->subject
            ->expects($this->any())
            ->method('parseData')
            ->will($this->returnValue('void'));

        $this->setUpSettings();

        $this->mockedObjectManager = $this->getMockBuilder(
            \TYPO3\CMS\Extbase\Object\ObjectManagerInterface::class
        )->getMock();
        $this->inject($this->subject, 'objectManager', $this->mockedObjectManager);

        $this->view = $this->getMockBuilder(
            \TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class
        )->getMock();
        $this->inject($this->subject, 'view', $this->view);

        $sessionHandler = $this->getMockBuilder(
            \Extcode\Cart\Service\SessionHandler::class
        )->getMock();
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
        $parserUtility = $this->getMockBuilder(\Extcode\Cart\Utility\ParserUtility::class)
            ->setMethods(['parseTaxClasses'])
            ->getMock();
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
        $cartUtility = $this->getMockBuilder(\Extcode\Cart\Utility\CartUtility::class)
            ->setMethods(['parseServices'])
            ->getMock();
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
        $configurationManager = $this->getMockBuilder(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::class
        )
            ->setMethods(['getConfiguration', 'setConfiguration', 'getContentObject', 'setContentObject', 'isFeatureEnabled'])
            ->getMock();
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

        $this->subject->showAction();
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
                \Extcode\Cart\Domain\Model\Order\BillingAddress::class
            ),
            'shippingAddress' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\ShippingAddress::class
            ),
        ];

        $this->view->expects(self::at(2))->method('assignMultiple')->with($assignArguments);

        $this->subject->showAction();
    }

    /**
     * @test
     */
    public function showCartPassesOrderItemToView()
    {
        $orderItem = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Order\Item::class)
            ->getMock();

        $assignArguments = [
            'orderItem' => $orderItem,
            'billingAddress' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\BillingAddress::class
            ),
            'shippingAddress' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\ShippingAddress::class
            ),
        ];

        $this->view->expects(self::at(2))->method('assignMultiple')->with($assignArguments);

        $this->subject->showAction($orderItem, null, null);
    }

    /**
     * @test
     */
    public function showCartPassesBillingAddressItemToView()
    {
        $billingAddress = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Order\BillingAddress::class)
            ->getMock();

        $assignArguments = [
            'orderItem' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\Item::class
            ),
            'billingAddress' => $billingAddress,
            'shippingAddress' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\ShippingAddress::class
            ),
        ];

        $this->view->expects(self::at(2))->method('assignMultiple')->with($assignArguments);

        $this->subject->showAction(null, $billingAddress, null);
    }

    /**
     * @test
     */
    public function showCartPassesShippingAddressItemToView()
    {
        $shippingAddress = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Order\ShippingAddress::class)
            ->getMock();

        $assignArguments = [
            'orderItem' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\Item::class
            ),
            'billingAddress' => $this->mockedObjectManager->get(
                \Extcode\Cart\Domain\Model\Order\BillingAddress::class
            ),
            'shippingAddress' => $shippingAddress
        ];

        $this->view->expects(self::at(2))->method('assignMultiple')->with($assignArguments);

        $this->subject->showAction(null, null, $shippingAddress);
    }
}
