<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\PaymentMethodsLoaderInterface;
use Extcode\Cart\Controller\Cart\OrderController;
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Service;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(OrderController::class)]
final class OrderControllerTest extends UnitTestCase
{
    /**
     * @param array<int, Service> $paymentMethods the payment methods of the cart, the cart uses the payment method 1
     */
    #[DataProvider('successRedirectUrls')]
    #[Test]
    public function getSuccessRedirectUrlReturnsTheUrlOfThePaymentMethod(array $paymentMethods, ?string $expectedUrl): void
    {
        $paymentMethodsLoader = self::createStub(PaymentMethodsLoaderInterface::class);
        $paymentMethodsLoader->method('getPaymentMethods')->willReturn($paymentMethods);

        $cart = self::createStub(Cart::class);
        $cart->method('getPayment')->willReturn(new Service(1, []));

        $subject = new OrderController();
        $subject->injectPaymentMethodsService($paymentMethodsLoader);

        $reflection = new ReflectionClass(OrderController::class);
        $reflection->getProperty('cart')->setValue($subject, $cart);

        self::assertSame($expectedUrl, $reflection->getMethod('getSuccessRedirectUrl')->invoke($subject));
    }

    /**
     * @return iterable<string, array{array<int, Service>, ?string}>
     */
    public static function successRedirectUrls(): iterable
    {
        yield 'url of the payment method of the cart' => [
            [1 => new Service(1, ['redirects' => ['success' => ['url' => ' https://example.com/thank-you/ ']]])],
            'https://example.com/thank-you/',
        ];
        yield 'no url' => [
            [1 => new Service(1, [])],
            null,
        ];
        yield 'empty url' => [
            [1 => new Service(1, ['redirects' => ['success' => ['url' => '']]])],
            null,
        ];
        yield 'url of another payment method' => [
            [2 => new Service(2, ['redirects' => ['success' => ['url' => 'https://example.com/thank-you/']]])],
            null,
        ];
    }
}
