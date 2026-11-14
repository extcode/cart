<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\EventListener\Order\Finish;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\PaymentMethodsLoaderInterface;
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Service;
use Extcode\Cart\Domain\Model\Cart\ServiceInterface;
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\Item as OrderItem;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;
use Extcode\Cart\Event\Order\FinishEvent;
use Extcode\Cart\EventListener\Order\Finish\ClearCart;
use Extcode\Cart\Service\SessionHandler;
use Extcode\Cart\Utility\CartUtility;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ClearCart::class)]
final class ClearCartTest extends UnitTestCase
{
    private const PAYMENT_ID = 2;

    private const SETTINGS = [
        'settings' => [
            'cart' => [
                'pid' => '3',
            ],
        ],
    ];

    private CartUtility&MockObject $cartUtility;

    private MockObject&PaymentMethodsLoaderInterface $paymentMethodsLoader;

    private MockObject&SessionHandler $sessionHandler;

    private Cart $cart;

    private ClearCart $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cartUtility = $this->createMock(CartUtility::class);
        $this->paymentMethodsLoader = $this->createMock(PaymentMethodsLoaderInterface::class);
        $this->sessionHandler = $this->createMock(SessionHandler::class);

        $payment = self::createStub(ServiceInterface::class);
        $payment->method('getId')->willReturn(self::PAYMENT_ID);

        $this->cart = self::createStub(Cart::class);
        $this->cart->method('getPayment')->willReturn($payment);

        $this->subject = new ClearCart(
            $this->cartUtility,
            $this->paymentMethodsLoader,
            $this->sessionHandler,
        );
    }

    #[Test]
    public function doesNotClearTheCartIfPreventClearCartIsSet(): void
    {
        $this->paymentMethodsLoader
            ->method('getPaymentMethods')
            ->willReturn(self::createPaymentMethods([
                self::PAYMENT_ID => [
                    'preventClearCart' => '1',
                ],
            ]))
        ;

        $this->cartUtility->expects(self::never())->method('getNewCart');
        $this->sessionHandler->expects(self::never())->method('writeCart');
        $this->sessionHandler->expects(self::never())->method('writeAddress');

        ($this->subject)($this->createEvent());
    }

    /**
     * @param array<int, array<string, string>> $paymentMethodConfigurations
     */
    #[DataProvider('paymentMethodsWithoutPreventClearCartDataProvider')]
    #[Test]
    public function clearsCartAndAddresses(array $paymentMethodConfigurations): void
    {
        $this->paymentMethodsLoader
            ->expects(self::once())
            ->method('getPaymentMethods')
            ->with($this->cart)
            ->willReturn(self::createPaymentMethods($paymentMethodConfigurations))
        ;

        $newCart = self::createStub(Cart::class);
        $this->cartUtility
            ->expects(self::once())
            ->method('getNewCart')
            ->with(self::SETTINGS)
            ->willReturn($newCart)
        ;

        $this->sessionHandler
            ->expects(self::once())
            ->method('writeCart')
            ->with('3', $newCart)
        ;

        $writtenAddresses = [];
        $this->sessionHandler
            ->expects(self::exactly(2))
            ->method('writeAddress')
            ->willReturnCallback(static function (string $key, object $address) use (&$writtenAddresses): void {
                $writtenAddresses[$key] = $address::class;
            })
        ;

        ($this->subject)($this->createEvent());

        self::assertSame(
            [
                'billing_address_3' => BillingAddress::class,
                'shipping_address_3' => ShippingAddress::class,
            ],
            $writtenAddresses
        );
    }

    /**
     * @return iterable<string, array{array<int, array<string, string>>}>
     */
    public static function paymentMethodsWithoutPreventClearCartDataProvider(): iterable
    {
        yield 'no payment methods' => [[]];
        yield 'preventClearCart not set' => [[self::PAYMENT_ID => []]];
        yield 'preventClearCart disabled' => [[self::PAYMENT_ID => ['preventClearCart' => '0']]];
        yield 'preventClearCart set for another payment' => [[self::PAYMENT_ID + 1 => ['preventClearCart' => '1']]];
    }

    /**
     * Creates the payment methods like PaymentMethodsLoaderInterface returns
     * them: services indexed by their id.
     *
     * @param array<int, array<string, string>> $configurations
     *
     * @return array<int, Service>
     */
    private static function createPaymentMethods(array $configurations): array
    {
        $paymentMethods = [];
        foreach ($configurations as $id => $configuration) {
            $paymentMethods[$id] = new Service($id, $configuration);
        }

        return $paymentMethods;
    }

    private function createEvent(): FinishEvent
    {
        return new FinishEvent($this->cart, new OrderItem(), self::SETTINGS);
    }
}
