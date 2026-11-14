<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Finisher\Form;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Finisher\Form\AddToCartFinisher;
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Event\Form\AddToCartFinisherEvent;
use Extcode\Cart\Service\SessionHandler;
use Extcode\Cart\Utility\CartUtility;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(AddToCartFinisher::class)]
final class AddToCartFinisherTest extends UnitTestCase
{
    private const CONFIGURATIONS = [
        'settings' => [
            'cart' => [
                'pid' => '3',
            ],
        ],
    ];

    #[Test]
    public function dispatchesEventWithNewCartIfNoCartIsStoredInSession(): void
    {
        $newCart = self::createStub(Cart::class);

        $configurationManager = self::createStub(ConfigurationManagerInterface::class);
        $configurationManager->method('getConfiguration')->willReturn(self::CONFIGURATIONS);

        $sessionHandler = $this->createMock(SessionHandler::class);
        $sessionHandler->expects(self::once())->method('restoreCart')->with('3')->willReturn(null);
        $sessionHandler->expects(self::once())->method('writeCart')->with('3', $newCart);

        $cartUtility = $this->createMock(CartUtility::class);
        $cartUtility->expects(self::once())->method('getNewCart')->with(self::CONFIGURATIONS)->willReturn($newCart);

        $dispatchedEvent = null;
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(static function (AddToCartFinisherEvent $event) use (&$dispatchedEvent): AddToCartFinisherEvent {
                $dispatchedEvent = $event;
                // Stop the finisher before it adds products and renders messages.
                $event->setErrors(['error']);

                return $event;
            })
        ;

        $subject = $this->getAccessibleMock(
            AddToCartFinisher::class,
            ['getFormValues', 'getHoneypotIdentifier'],
            [
                $configurationManager,
                $sessionHandler,
                $cartUtility,
                $eventDispatcher,
            ]
        );
        $subject->expects(self::once())->method('getFormValues')->willReturn(['productType' => 'test']);
        $subject->expects(self::once())->method('getHoneypotIdentifier')->willReturn('');

        $subject->_call('executeInternal');

        self::assertInstanceOf(AddToCartFinisherEvent::class, $dispatchedEvent);
        self::assertSame($newCart, $dispatchedEvent->getCart());
    }
}
