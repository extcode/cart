<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Finish;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;
use Extcode\Cart\Event\Order\EventInterface;
use Extcode\Cart\Service\SessionHandler;
use Extcode\Cart\Utility\CartUtility;
use Extcode\Cart\Utility\ParserUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClearCart
{
    /**
     * @var CartUtility
     */
    protected $cartUtility;

    /**
     * @var ParserUtility
     */
    protected $parserUtility;

    /**
     * @var SessionHandler
     */
    protected $sessionHandler;

    public function __construct(
        CartUtility $cartUtility,
        ParserUtility $parserUtility,
        SessionHandler $sessionHandler
    ) {
        $this->cartUtility = $cartUtility;
        $this->parserUtility = $parserUtility;
        $this->sessionHandler = $sessionHandler;
    }

    public function __invoke(EventInterface $event): void
    {
        $cart = $event->getCart();
        $settings = $event->getSettings();
        $cartPid = $settings['settings']['cart']['pid'];
        $this->sessionHandler->writeCart(
            $cartPid,
            $this->cartUtility->getNewCart($settings)
        );
        $this->sessionHandler->writeAddress(
            'billing_address_' . $cartPid,
            GeneralUtility::makeInstance(BillingAddress::class)
        );
    }
}
