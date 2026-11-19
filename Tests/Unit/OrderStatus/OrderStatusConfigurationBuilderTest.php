<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\OrderStatus;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\OrderStatus\OrderStatusConfigurationBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use UnexpectedValueException;

#[CoversClass(OrderStatusConfigurationBuilder::class)]
final class OrderStatusConfigurationBuilderTest extends UnitTestCase
{
    private const CART_PAYMENT_STATUS = ['open', 'pending', 'paid', 'canceled'];

    private const CART_SHIPPING_STATUS = ['open', 'on_hold', 'in_process', 'shipped'];

    private OrderStatusConfigurationBuilder $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new OrderStatusConfigurationBuilder(new DependencyOrderingService());
    }

    #[Test]
    public function buildReturnsTheStatusOfCartInTheirOrder(): void
    {
        $result = $this->subject->build(['cart' => self::getCartConfiguration()]);

        self::assertSame(self::CART_PAYMENT_STATUS, array_keys($result['payment']));
        self::assertSame(self::CART_SHIPPING_STATUS, array_keys($result['shipping']));
        self::assertSame(
            'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:tx_cart_domain_model_order_payment.status.paid',
            $result['payment']['paid']
        );
    }

    #[Test]
    public function buildReturnsBothServiceTypesWithoutAnyConfiguration(): void
    {
        self::assertSame(
            ['payment' => [], 'shipping' => []],
            $this->subject->build([])
        );
    }

    /**
     * @param array<string, mixed> $configurations
     * @param list<string> $expectedPaymentStatus
     * @param list<string> $expectedShippingStatus
     */
    #[DataProvider('ownStatusDataProvider')]
    #[Test]
    public function buildOrdersOwnStatusOfOtherExtensions(
        array $configurations,
        array $expectedPaymentStatus,
        array $expectedShippingStatus
    ): void {
        $result = $this->subject->build(array_merge(['cart' => self::getCartConfiguration()], $configurations));

        self::assertSame($expectedPaymentStatus, array_keys($result['payment']));
        self::assertSame($expectedShippingStatus, array_keys($result['shipping']));
    }

    /**
     * @return iterable<string, array{configurations: array<string, mixed>, expectedPaymentStatus: list<string>, expectedShippingStatus: list<string>}>
     */
    public static function ownStatusDataProvider(): iterable
    {
        yield 'payment status before the first status' => [
            'configurations' => [
                'my_ext' => [
                    'payment' => [
                        'new' => ['label' => 'New', 'before' => ['open']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => ['new', 'open', 'pending', 'paid', 'canceled'],
            'expectedShippingStatus' => self::CART_SHIPPING_STATUS,
        ];
        yield 'payment status between two status' => [
            'configurations' => [
                'my_ext' => [
                    'payment' => [
                        'refunded' => ['label' => 'Refunded', 'after' => ['paid'], 'before' => ['canceled']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => ['open', 'pending', 'paid', 'refunded', 'canceled'],
            'expectedShippingStatus' => self::CART_SHIPPING_STATUS,
        ];
        yield 'payment status after the last status' => [
            'configurations' => [
                'my_ext' => [
                    'payment' => [
                        'chargeback' => ['label' => 'Chargeback', 'after' => ['canceled']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => ['open', 'pending', 'paid', 'canceled', 'chargeback'],
            'expectedShippingStatus' => self::CART_SHIPPING_STATUS,
        ];
        yield 'shipping status before the first status' => [
            'configurations' => [
                'my_ext' => [
                    'shipping' => [
                        'ordered' => ['label' => 'Ordered', 'before' => ['open']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => self::CART_PAYMENT_STATUS,
            'expectedShippingStatus' => ['ordered', 'open', 'on_hold', 'in_process', 'shipped'],
        ];
        yield 'shipping status between two status' => [
            'configurations' => [
                'my_ext' => [
                    'shipping' => [
                        'packed' => ['label' => 'Packed', 'after' => ['in_process'], 'before' => ['shipped']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => self::CART_PAYMENT_STATUS,
            'expectedShippingStatus' => ['open', 'on_hold', 'in_process', 'packed', 'shipped'],
        ];
        yield 'shipping status after the last status' => [
            'configurations' => [
                'my_ext' => [
                    'shipping' => [
                        'delivered' => ['label' => 'Delivered', 'after' => ['shipped']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => self::CART_PAYMENT_STATUS,
            'expectedShippingStatus' => ['open', 'on_hold', 'in_process', 'shipped', 'delivered'],
        ];
        yield 'status at all positions of both service types' => [
            'configurations' => [
                'my_ext' => [
                    'payment' => [
                        'chargeback' => ['label' => 'Chargeback', 'after' => ['canceled']],
                        'refunded' => ['label' => 'Refunded', 'after' => ['paid'], 'before' => ['canceled']],
                        'new' => ['label' => 'New', 'before' => ['open']],
                    ],
                    'shipping' => [
                        'delivered' => ['label' => 'Delivered', 'after' => ['shipped']],
                        'packed' => ['label' => 'Packed', 'after' => ['in_process'], 'before' => ['shipped']],
                        'ordered' => ['label' => 'Ordered', 'before' => ['open']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => ['new', 'open', 'pending', 'paid', 'refunded', 'canceled', 'chargeback'],
            'expectedShippingStatus' => ['ordered', 'open', 'on_hold', 'in_process', 'packed', 'shipped', 'delivered'],
        ];
        yield 'two status between the same two status' => [
            'configurations' => [
                'my_ext' => [
                    'payment' => [
                        'refunded' => ['label' => 'Refunded', 'after' => ['partially_refunded'], 'before' => ['canceled']],
                        'partially_refunded' => ['label' => 'Partially refunded', 'after' => ['paid'], 'before' => ['refunded']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => ['open', 'pending', 'paid', 'partially_refunded', 'refunded', 'canceled'],
            'expectedShippingStatus' => self::CART_SHIPPING_STATUS,
        ];
        yield 'status relative to the status of another extension' => [
            'configurations' => [
                'my_ext' => [
                    'payment' => [
                        'refunded' => ['label' => 'Refunded', 'after' => ['paid'], 'before' => ['canceled']],
                    ],
                ],
                'my_other_ext' => [
                    'payment' => [
                        'partially_refunded' => ['label' => 'Partially refunded', 'after' => ['paid'], 'before' => ['refunded']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => ['open', 'pending', 'paid', 'partially_refunded', 'refunded', 'canceled'],
            'expectedShippingStatus' => self::CART_SHIPPING_STATUS,
        ];
        yield 'status relative to the status of an extension loaded later' => [
            'configurations' => [
                'my_ext' => [
                    'payment' => [
                        'partially_refunded' => ['label' => 'Partially refunded', 'after' => ['paid'], 'before' => ['refunded']],
                    ],
                ],
                'my_other_ext' => [
                    'payment' => [
                        'refunded' => ['label' => 'Refunded', 'after' => ['paid'], 'before' => ['canceled']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => ['open', 'pending', 'paid', 'partially_refunded', 'refunded', 'canceled'],
            'expectedShippingStatus' => self::CART_SHIPPING_STATUS,
        ];
        yield 'status of cart moved by another extension' => [
            'configurations' => [
                'my_ext' => [
                    'payment' => [
                        'canceled' => ['after' => ['open'], 'before' => ['pending']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => ['open', 'canceled', 'pending', 'paid'],
            'expectedShippingStatus' => self::CART_SHIPPING_STATUS,
        ];
        yield 'status of cart with a changed label keeps its position' => [
            'configurations' => [
                'my_ext' => [
                    'payment' => [
                        'pending' => ['label' => 'Awaiting payment'],
                    ],
                ],
            ],
            'expectedPaymentStatus' => self::CART_PAYMENT_STATUS,
            'expectedShippingStatus' => self::CART_SHIPPING_STATUS,
        ];
        yield 'reference to a status which is not registered is ignored' => [
            'configurations' => [
                'my_ext' => [
                    'payment' => [
                        'refunded' => ['label' => 'Refunded', 'after' => ['paid', 'not_registered'], 'before' => ['canceled']],
                    ],
                ],
            ],
            'expectedPaymentStatus' => ['open', 'pending', 'paid', 'refunded', 'canceled'],
            'expectedShippingStatus' => self::CART_SHIPPING_STATUS,
        ];
    }

    #[Test]
    public function buildReplacesTheLabelOfAStatusByTheOneOfALaterExtension(): void
    {
        $result = $this->subject->build([
            'cart' => self::getCartConfiguration(),
            'my_ext' => [
                'payment' => [
                    'pending' => ['label' => 'Awaiting payment'],
                ],
            ],
        ]);

        self::assertSame('Awaiting payment', $result['payment']['pending']);
        self::assertSame(
            'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:tx_cart_domain_model_order_shipping.status.open',
            $result['shipping']['open'],
            'A status of another service type with the same identifier must not be changed.'
        );
    }

    /**
     * @param array<string, mixed> $configurations
     */
    #[DataProvider('invalidConfigurationDataProvider')]
    #[Test]
    public function buildThrowsExceptionForInvalidConfiguration(array $configurations, UnexpectedValueException $expectedException): void
    {
        $this->expectExceptionObject($expectedException);

        $this->subject->build(array_merge(['cart' => self::getCartConfiguration()], $configurations));
    }

    /**
     * @return iterable<string, array{configurations: array<string, mixed>, expectedException: UnexpectedValueException}>
     */
    public static function invalidConfigurationDataProvider(): iterable
    {
        yield 'configuration is no array' => [
            'configurations' => ['my_ext' => 1],
            'expectedException' => new UnexpectedValueException(
                'The Configuration/OrderStatus.php of the package "my_ext" must return an array.',
                1795068606
            ),
        ];
        yield 'unknown service type' => [
            'configurations' => ['my_ext' => ['invoice' => []]],
            'expectedException' => new UnexpectedValueException(
                'The Configuration/OrderStatus.php of the package "my_ext" contains the unknown service type "invoice", allowed are "payment", "shipping".',
                1795068607
            ),
        ];
        yield 'status of a service type are no array' => [
            'configurations' => ['my_ext' => ['payment' => 'refunded']],
            'expectedException' => new UnexpectedValueException(
                'The payment status of the Configuration/OrderStatus.php of the package "my_ext" must be an array.',
                1795068608
            ),
        ];
        yield 'status without label' => [
            'configurations' => ['my_ext' => ['payment' => ['refunded' => ['after' => ['paid']]]]],
            'expectedException' => new UnexpectedValueException(
                'The payment status "refunded" has no label.',
                1795068609
            ),
        ];
        yield 'invalid identifier' => [
            'configurations' => ['my_ext' => ['payment' => ['Refunded' => ['label' => 'Refunded']]]],
            'expectedException' => new UnexpectedValueException(
                'The payment status "Refunded" of the Configuration/OrderStatus.php of the package "my_ext" has an invalid identifier, it must match /^[a-z][a-z0-9_]*$/.',
                1795068610
            ),
        ];
        yield 'status is no array' => [
            'configurations' => ['my_ext' => ['payment' => ['refunded' => 'Refunded']]],
            'expectedException' => new UnexpectedValueException(
                'The payment status "refunded" of the Configuration/OrderStatus.php of the package "my_ext" must be an array.',
                1795068611
            ),
        ];
        yield 'unknown key' => [
            'configurations' => ['my_ext' => ['payment' => ['refunded' => ['label' => 'Refunded', 'sorting' => 10]]]],
            'expectedException' => new UnexpectedValueException(
                'The payment status "refunded" of the Configuration/OrderStatus.php of the package "my_ext" contains the unknown keys "sorting", allowed are "label", "before", "after".',
                1795068612
            ),
        ];
        yield 'empty label' => [
            'configurations' => ['my_ext' => ['shipping' => ['packed' => ['label' => '']]]],
            'expectedException' => new UnexpectedValueException(
                'The shipping status "packed" of the Configuration/OrderStatus.php of the package "my_ext" must have a label of type non-empty string.',
                1795068613
            ),
        ];
        yield 'reference as string instead of list' => [
            'configurations' => ['my_ext' => ['shipping' => ['packed' => ['label' => 'Packed', 'before' => 'shipped']]]],
            'expectedException' => new UnexpectedValueException(
                'The shipping status "packed" of the Configuration/OrderStatus.php of the package "my_ext" must have a list of status identifiers in "before".',
                1795068614
            ),
        ];
        yield 'cycle' => [
            'configurations' => ['my_ext' => ['payment' => ['refunded' => ['label' => 'Refunded', 'after' => ['canceled'], 'before' => ['paid']]]]],
            'expectedException' => new UnexpectedValueException(
                'The payment status cannot be ordered: Your dependencies have cycles.',
                1795068615
            ),
        ];
    }

    /**
     * @return array<array-key, mixed>
     */
    private static function getCartConfiguration(): array
    {
        $configuration = require __DIR__ . '/../../../Configuration/OrderStatus.php';
        self::assertIsArray($configuration);

        return $configuration;
    }
}
