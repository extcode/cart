<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\EventListener\Order\Finish;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\MailConfigurationLoaderInterface;
use Extcode\Cart\Configuration\Loader\PaymentMethodsLoaderInterface;
use Extcode\Cart\Domain\Log\LogServiceInterface;
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Service;
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\Item as OrderItem;
use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Event\Order\FinishEvent;
use Extcode\Cart\EventListener\Order\Finish\Email;
use Extcode\Cart\Mail\BuyerMailConfiguration;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\Mail\Mailer;
use Extcode\Cart\Mail\OrderMailSender;
use Extcode\Cart\Service\MailHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Configuration\Features;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Email::class)]
final class EmailTest extends UnitTestCase
{
    #[Test]
    public function withoutFeatureToggleTheMailHandlerSendsTheEmails(): void
    {
        $mailHandler = $this->createMock(MailHandler::class);
        $mailHandler->expects(self::once())->method('sendBuyerMail');
        $mailHandler->expects(self::once())->method('sendSellerMail');

        $mailConfigurationLoader = $this->createMock(MailConfigurationLoaderInterface::class);
        $mailConfigurationLoader->expects(self::never())->method('getBuyerMailConfiguration');
        $mailConfigurationLoader->expects(self::never())->method('getSellerMailConfiguration');

        $subject = self::createSubject(false, $mailHandler, $mailConfigurationLoader);

        $subject(self::createEvent());
    }

    #[Test]
    public function withFeatureToggleTheOrderMailSenderSendsTheEmails(): void
    {
        $mailHandler = $this->createMock(MailHandler::class);
        $mailHandler->expects(self::never())->method('sendBuyerMail');
        $mailHandler->expects(self::never())->method('sendSellerMail');

        $mailConfigurationLoader = $this->createMock(MailConfigurationLoaderInterface::class);
        $mailConfigurationLoader->expects(self::once())->method('getBuyerMailConfiguration')
            ->with('Mail/Open/Buyer', self::isInstanceOf(OrderItem::class))
            ->willReturn(new BuyerMailConfiguration('Mail/Open/Buyer'))
        ;
        $mailConfigurationLoader->expects(self::once())->method('getSellerMailConfiguration')
            ->with('Mail/Open/Seller', self::isInstanceOf(OrderItem::class))
            ->willThrowException(new InvalidMailConfigurationException('The seller has no "toAddress".', 1795110002))
        ;

        $subject = self::createSubject(true, $mailHandler, $mailConfigurationLoader);

        $subject(self::createEvent());
    }

    #[Test]
    public function withoutThePaymentMethodOfTheOrderBothEmailsAreSent(): void
    {
        $mailHandler = $this->createMock(MailHandler::class);
        $mailHandler->expects(self::once())->method('sendBuyerMail');
        $mailHandler->expects(self::once())->method('sendSellerMail');

        $subject = self::createSubject(false, $mailHandler, $this->createMock(MailConfigurationLoaderInterface::class), []);

        $subject(self::createEvent());
    }

    #[Test]
    public function theEmailsDisabledByThePaymentMethodAreNotSent(): void
    {
        $mailHandler = $this->createMock(MailHandler::class);
        $mailHandler->expects(self::never())->method('sendBuyerMail');
        $mailHandler->expects(self::once())->method('sendSellerMail');

        $subject = self::createSubject(false, $mailHandler, $this->createMock(MailConfigurationLoaderInterface::class), [1 => self::createPaymentMethod(true, false)]);

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
     * @param null|array<int, Service> $paymentMethods the payment methods of the cart, default the one of the order
     */
    private static function createSubject(
        bool $featureEnabled,
        MailHandler&MockObject $mailHandler,
        MailConfigurationLoaderInterface&MockObject $mailConfigurationLoader,
        ?array $paymentMethods = null,
    ): Email {
        $features = self::createStub(Features::class);
        $features->method('isFeatureEnabled')->willReturnMap([[Email::FEATURE, $featureEnabled]]);

        $paymentMethodsLoader = self::createStub(PaymentMethodsLoaderInterface::class);
        $paymentMethodsLoader->method('getPaymentMethods')->willReturn($paymentMethods ?? [1 => self::createPaymentMethod(false, false)]);

        return new Email(
            $paymentMethodsLoader,
            $mailHandler,
            new OrderMailSender(
                $mailConfigurationLoader,
                new Mailer(self::createStub(MailerInterface::class)),
                self::createStub(ConfigurationManagerInterface::class),
                self::createStub(EventDispatcherInterface::class),
                self::createStub(LogServiceInterface::class),
            ),
            $features,
        );
    }

    private static function createEvent(): FinishEvent
    {
        $billingAddress = self::createStub(BillingAddress::class);
        $billingAddress->method('getEmail')->willReturn('customer@example.com');

        $payment = self::createStub(Payment::class);
        $payment->method('getStatus')->willReturn('open');
        $payment->method('getServiceId')->willReturn(1);

        $orderItem = self::createStub(OrderItem::class);
        $orderItem->method('getBillingAddress')->willReturn($billingAddress);
        $orderItem->method('getPayment')->willReturn($payment);
        $orderItem->method('getUid')->willReturn(142);

        return new FinishEvent(self::createStub(Cart::class), $orderItem);
    }
}
