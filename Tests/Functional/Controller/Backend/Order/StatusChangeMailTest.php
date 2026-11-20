<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\EventListener\Order\StatusChange\Mail;
use Extcode\Cart\OrderStatus\PaymentStatus;
use Extcode\Cart\OrderStatus\ShippingStatus;
use Extcode\Cart\OrderStatus\StatusChangeService;
use Extcode\Cart\Tests\Functional\Mail\CollectingTransport;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Emails of status changes configured in plugin.tx_cart.statusChange.mail,
 * sent when an editor changes the status in the order details.
 */
#[CoversClass(Mail::class)]
final class StatusChangeMailTest extends AbstractOrderStatusTestCase
{
    private const PAYMENT_UPDATE_ROUTE = 'cart_cart_orders.Backend\Order\Payment_update';

    private const SHIPPING_UPDATE_ROUTE = 'cart_cart_orders.Backend\Order\Shipping_update';

    /**
     * Page uid of the root page of the order storage, see Tests/Fixtures/BaseDatabase.php
     */
    private const SHOP_ROOT_PID = 101;

    private const MAIL_TYPOSCRIPT = <<<'TYPOSCRIPT'
        plugin.tx_cart.mail {
            buyer {
                fromName = Shop
                fromAddress = shop@example.com
                bccAddress = archive@example.com
            }
            seller {
                fromAddress = shop@example.com
                toAddress = orders@example.com
            }
        }
        TYPOSCRIPT;

    protected array $configurationToUseInTestInstance = [
        'MAIL' => [
            'transport' => CollectingTransport::class,
            'templateRootPaths' => [
                1795110000 => 'EXT:cart/Tests/Functional/Fixtures/Templates/',
            ],
        ],
        'SYS' => [
            'features' => [
                Mail::FEATURE => true,
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        CollectingTransport::reset();
        $this->createOrder(1, PaymentStatus::OPEN, ShippingStatus::OPEN);
        $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_address')->update(
            'tx_cart_domain_model_order_address',
            ['email' => 'customer@example.com'],
            ['uid' => 1]
        );
    }

    #[Test]
    public function aConfiguredTransitionSendsTheEmailToTheBuyer(): void
    {
        $this->setStatusChangeTypoScript(<<<'TYPOSCRIPT'
            plugin.tx_cart.statusChange.mail.payment {
                openToPaid {
                    from = open
                    to = paid
                    recipients.buyer < plugin.tx_cart.mail.buyer
                }
            }
            TYPOSCRIPT);

        $html = self::changePaymentStatus(PaymentStatus::PAID);

        $emails = CollectingTransport::getEmails();
        self::assertCount(1, $emails);
        self::assertSame(['customer@example.com'], self::getAddresses($emails[0]->getTo()));
        self::assertSame(['shop@example.com'], self::getAddresses($emails[0]->getFrom()));
        self::assertSame('Shop', $emails[0]->getFrom()[0]->getName());
        self::assertSame(['archive@example.com'], self::getAddresses($emails[0]->getBcc()));
        self::assertSame('Payment/OpenToPaid/Buyer O-001', $emails[0]->getSubject());
        self::assertStringContainsString('Payment/OpenToPaid/Buyer: open to paid', (string) $emails[0]->getHtmlBody());
        self::assertStringContainsString('The payment was successfully updated.', $html);
        self::assertStringNotContainsString('[ERROR]', $html);
        self::assertSame(
            [['info', 'The email "openToPaid" was sent to the buyer.']],
            $this->getOrderLog()
        );
    }

    #[Test]
    public function anyOldStatusSendsTheEmailsToTheBuyerAndTheSeller(): void
    {
        $this->setStatusChangeTypoScript(<<<'TYPOSCRIPT'
            plugin.tx_cart.statusChange.mail.payment {
                anyToCanceled {
                    from = *
                    to = canceled
                    recipients {
                        buyer < plugin.tx_cart.mail.buyer
                        seller < plugin.tx_cart.mail.seller
                    }
                }
            }
            TYPOSCRIPT);

        self::changePaymentStatus(PaymentStatus::CANCELED);

        $emails = CollectingTransport::getEmails();
        self::assertCount(2, $emails);
        self::assertSame(['customer@example.com'], self::getAddresses($emails[0]->getTo()));
        self::assertSame('Payment/AnyToCanceled/Buyer O-001', $emails[0]->getSubject());
        self::assertSame(['orders@example.com'], self::getAddresses($emails[1]->getTo()));
        self::assertSame(['customer@example.com'], self::getAddresses($emails[1]->getReplyTo()), 'The seller replies to the buyer.');
        self::assertSame('Payment/AnyToCanceled/Seller O-001', $emails[1]->getSubject());
    }

    #[Test]
    public function aListOfOldStatusAndAnExplicitTemplateSendTheEmail(): void
    {
        $this->setStatusChangeTypoScript(<<<'TYPOSCRIPT'
            plugin.tx_cart.statusChange.mail.shipping {
                10 {
                    from = in_process, open
                    to = shipped
                    template = Mail/StatusChange/Shipped
                    recipients.seller < plugin.tx_cart.mail.seller
                }
            }
            TYPOSCRIPT);

        self::submitForm(
            (string) self::dispatchShowAction(1)->getBody(),
            'shippingStatus',
            self::SHIPPING_UPDATE_ROUTE,
            ['status' => ShippingStatus::SHIPPED]
        );

        $emails = CollectingTransport::getEmails();
        self::assertCount(1, $emails);
        self::assertSame('Shipped/Seller O-001', $emails[0]->getSubject());
    }

    #[Test]
    public function noEmailIsSentWithoutTheFeatureToggle(): void
    {
        $configuration = $GLOBALS['TYPO3_CONF_VARS'];
        self::assertIsArray($configuration);
        $GLOBALS['TYPO3_CONF_VARS'] = ArrayUtility::setValueByPath($configuration, ['SYS', 'features', Mail::FEATURE], false);
        $this->setStatusChangeTypoScript(<<<'TYPOSCRIPT'
            plugin.tx_cart.statusChange.mail.payment {
                openToPaid {
                    from = open
                    to = paid
                    recipients.buyer < plugin.tx_cart.mail.buyer
                }
                invalid = 1
            }
            TYPOSCRIPT);

        $html = $this->changePaymentStatus(PaymentStatus::PAID);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame(PaymentStatus::PAID, $this->getStoredPaymentStatus());
        self::assertStringNotContainsString('[ERROR]', $html, 'Even an invalid configuration is ignored.');
        self::assertSame([], $this->getOrderLog());
    }

    #[Test]
    public function anotherTransitionSendsNoEmail(): void
    {
        $this->setStatusChangeTypoScript(<<<'TYPOSCRIPT'
            plugin.tx_cart.statusChange.mail.payment {
                openToPaid {
                    from = open
                    to = paid
                    recipients.buyer < plugin.tx_cart.mail.buyer
                }
            }
            plugin.tx_cart.statusChange.mail.shipping {
                pendingToShipped {
                    from = pending
                    to = shipped
                    recipients.buyer < plugin.tx_cart.mail.buyer
                }
            }
            TYPOSCRIPT);

        $html = self::changePaymentStatus(PaymentStatus::PENDING);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertStringNotContainsString('[ERROR]', $html);
        self::assertSame([], $this->getOrderLog());
    }

    #[Test]
    public function aMissingTemplateShowsAnErrorAndTheStatusIsChangedAnyway(): void
    {
        $this->setStatusChangeTypoScript(<<<'TYPOSCRIPT'
            plugin.tx_cart.statusChange.mail.payment {
                openToPending {
                    from = open
                    to = pending
                    recipients.buyer < plugin.tx_cart.mail.buyer
                }
            }
            TYPOSCRIPT);

        $html = self::changePaymentStatus(PaymentStatus::PENDING);

        $message = 'The email "openToPending" to the buyer was not sent: The template "Mail/StatusChange/Payment/OpenToPending/Buyer" is missing.';
        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame(PaymentStatus::PENDING, $this->getStoredPaymentStatus());
        self::assertStringContainsString('The payment was successfully updated.', $html);
        self::assertStringContainsString('[ERROR] ' . $message, $html);
        self::assertSame([['error', $message]], $this->getOrderLog());
    }

    #[Test]
    public function anInvalidConfigurationShowsAnError(): void
    {
        $this->setStatusChangeTypoScript(<<<'TYPOSCRIPT'
            plugin.tx_cart.statusChange.mail.payment {
                10 {
                    from = open
                    to = paid
                    recipients.buyer < plugin.tx_cart.mail.buyer
                }
            }
            TYPOSCRIPT);

        $html = self::changePaymentStatus(PaymentStatus::PAID);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertStringContainsString(
            '[ERROR] The email configuration "10" is invalid, no email was sent: The key is no valid template folder, it must match /^[a-zA-Z][a-zA-Z0-9]*$/, or set "template".',
            $html
        );
    }

    #[Test]
    public function anOrderWithoutEmailOfTheBuyerShowsAnError(): void
    {
        $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_address')->update(
            'tx_cart_domain_model_order_address',
            ['email' => ''],
            ['uid' => 1]
        );
        $this->setStatusChangeTypoScript(<<<'TYPOSCRIPT'
            plugin.tx_cart.statusChange.mail.payment {
                openToPaid {
                    from = open
                    to = paid
                    recipients.buyer < plugin.tx_cart.mail.buyer
                }
            }
            TYPOSCRIPT);

        $html = self::changePaymentStatus(PaymentStatus::PAID);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertStringContainsString(
            '[ERROR] The email "openToPaid" to the buyer was not sent: The order has no email address of the buyer.',
            $html
        );
    }

    #[Test]
    public function anInvalidEmailAddressOfTheBuyerShowsAnErrorAndTheStatusIsChangedAnyway(): void
    {
        $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_address')->update(
            'tx_cart_domain_model_order_address',
            ['email' => 'customer'],
            ['uid' => 1]
        );
        $this->setStatusChangeTypoScript(<<<'TYPOSCRIPT'
            plugin.tx_cart.statusChange.mail.payment {
                openToPaid {
                    from = open
                    to = paid
                    recipients.buyer < plugin.tx_cart.mail.buyer
                }
            }
            TYPOSCRIPT);

        $html = $this->changePaymentStatus(PaymentStatus::PAID);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame(PaymentStatus::PAID, $this->getStoredPaymentStatus());
        self::assertStringContainsString(
            '[ERROR] The email "openToPaid" to the buyer was not sent: The email address "customer" is invalid.',
            $html
        );
    }

    #[Test]
    public function anInvalidEmailAddressInTypoScriptShowsAnError(): void
    {
        $this->setStatusChangeTypoScript(<<<'TYPOSCRIPT'
            plugin.tx_cart.statusChange.mail.payment {
                openToPaid {
                    from = open
                    to = paid
                    recipients.buyer < plugin.tx_cart.mail.buyer
                    recipients.buyer.bccAddress = archive
                }
            }
            TYPOSCRIPT);

        $html = $this->changePaymentStatus(PaymentStatus::PAID);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertStringContainsString(
            '[ERROR] The email configuration "openToPaid" is invalid, no email was sent: The email address "archive" of "bccAddress" is invalid.',
            $html
        );
    }

    #[Test]
    public function aChangeWithoutTypoScriptSendsNoEmailAndLogsAWarning(): void
    {
        // e.g. on the command line
        unset($GLOBALS['TYPO3_REQUEST']);
        $this->setStatusChangeTypoScript(<<<'TYPOSCRIPT'
            plugin.tx_cart.statusChange.mail.payment {
                openToPaid {
                    from = open
                    to = paid
                    recipients.buyer < plugin.tx_cart.mail.buyer
                }
            }
            TYPOSCRIPT);
        $payment = GeneralUtility::makeInstance(PersistenceManager::class)->getObjectByIdentifier(1, Payment::class);
        self::assertInstanceOf(Payment::class, $payment);
        $statusChangeService = $this->get(StatusChangeService::class);
        self::assertInstanceOf(StatusChangeService::class, $statusChangeService);

        $result = $statusChangeService->change($payment, PaymentStatus::PAID, PaymentStatus::OPEN);

        self::assertTrue($result->isChanged());
        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame(
            [['warning', 'No emails of status changes are sent, because the TypoScript is not available, e.g. on the command line.']],
            $this->getOrderLog()
        );
    }

    /**
     * @return string the HTML of the order details after the change
     */
    private static function changePaymentStatus(string $status): string
    {
        self::submitForm(
            (string) self::dispatchShowAction(1)->getBody(),
            'paymentStatus',
            self::PAYMENT_UPDATE_ROUTE,
            ['status' => $status]
        );

        return (string) self::dispatchShowAction(1)->getBody();
    }

    private function setStatusChangeTypoScript(string $typoScript): void
    {
        $this->getConnectionPool()->getConnectionForTable('sys_template')->insert(
            'sys_template',
            [
                'uid' => 1,
                'pid' => self::SHOP_ROOT_PID,
                'title' => 'Shop',
                'root' => 1,
                'clear' => 3,
                'config' => self::MAIL_TYPOSCRIPT . "\n" . $typoScript,
            ]
        );
    }

    private function getStoredPaymentStatus(): mixed
    {
        $row = $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_payment')
            ->select(['status'], 'tx_cart_domain_model_order_payment', ['uid' => 1])
            ->fetchAssociative()
        ;

        return is_array($row) ? $row['status'] : null;
    }

    /**
     * @return list<array{mixed, mixed}> level and message of the entries of the status change emails
     */
    private function getOrderLog(): array
    {
        $rows = $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_log')
            ->select(['log_level', 'message'], 'tx_cart_domain_model_order_log', ['item' => 1, 'type' => 'statusChangeMail'], [], ['uid' => 'ASC'])
            ->fetchAllAssociative()
        ;

        return array_map(static fn (array $row): array => [$row['log_level'], $row['message']], $rows);
    }

    /**
     * @param array<Address> $addresses
     *
     * @return list<string>
     */
    private static function getAddresses(array $addresses): array
    {
        return array_values(array_map(static fn (Address $address): string => $address->getAddress(), $addresses));
    }
}
