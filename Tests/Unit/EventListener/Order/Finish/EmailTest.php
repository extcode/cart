<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\EventListener\Order\Finish;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Service;
use Extcode\Cart\Domain\Model\Order\Item as OrderItem;
use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Event\Order\FinishEvent;
use Extcode\Cart\EventListener\Order\Finish\Email;
use Extcode\Cart\Service\MailHandler;
use Extcode\Cart\Service\PaymentMethodsServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Email::class)]
final class EmailTest extends UnitTestCase
{
    #[Test]
    public function withoutThePaymentMethodOfTheOrderBothEmailsAreSent(): void
    {
        $mailHandler = $this->createMock(MailHandler::class);
        $mailHandler->expects(self::once())->method('sendBuyerMail');
        $mailHandler->expects(self::once())->method('sendSellerMail');

        $subject = self::createSubject($mailHandler, []);

        $subject(self::createEvent());
    }

    #[Test]
    public function theEmailsDisabledByThePaymentMethodAreNotSent(): void
    {
        $mailHandler = $this->createMock(MailHandler::class);
        $mailHandler->expects(self::never())->method('sendBuyerMail');
        $mailHandler->expects(self::once())->method('sendSellerMail');

        $subject = self::createSubject($mailHandler, [1 => self::createPaymentMethod(true, false)]);

        $subject(self::createEvent());
    }

    private static function createPaymentMethod(bool $buyerEmailDisabled, bool $sellerEmailDisabled): Service
    {
        $paymentMethod = self::createStub(Service::class);
        $paymentMethod->method('isBuyerEmailDisabled')->willReturn($buyerEmailDisabled);
        $paymentMethod->method('isSellerEmailDisabled')->willReturn($sellerEmailDisabled);

        return $paymentMethod;
    }

    /**
     * @param array<int, Service> $paymentMethods the payment methods of the cart
     */
    private static function createSubject(MailHandler&MockObject $mailHandler, array $paymentMethods): Email
    {
        $paymentMethodsService = self::createStub(PaymentMethodsServiceInterface::class);
        $paymentMethodsService->method('getPaymentMethods')->willReturn($paymentMethods);

        return new Email($paymentMethodsService, $mailHandler);
    }

    private static function createEvent(): FinishEvent
    {
        $payment = self::createStub(Payment::class);
        $payment->method('getServiceId')->willReturn(1);

        $orderItem = self::createStub(OrderItem::class);
        $orderItem->method('getPayment')->willReturn($payment);

        return new FinishEvent(self::createStub(Cart::class), $orderItem);
    }
}
