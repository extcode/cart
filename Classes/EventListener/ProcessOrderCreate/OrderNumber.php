<?php
declare(strict_types=1);
namespace Extcode\Cart\EventListener\ProcessOrderCreate;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Repository\Order\ItemRepository as OrderItemRepository;
use Extcode\Cart\Event\Order\EventInterface;
use Extcode\Cart\Utility\OrderUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class OrderNumber
{
    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var OrderUtility
     */
    protected $orderUtility;

    public function __construct(
        PersistenceManager $persistenceManager,
        OrderItemRepository $orderItemRepository,
        OrderUtility $orderUtility
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderUtility = $orderUtility;
    }

    public function __invoke(EventInterface $event): void
    {
        $cart = $event->getCart();
        $orderItem = $event->getOrderItem();

        $orderNumber = $this->orderUtility->getNumber($event->getSettings(), 'order');

        $orderItem->setOrderNumber($orderNumber);
        $orderItem->setOrderDate(new \DateTime());

        $this->orderItemRepository->update($orderItem);

        $this->persistenceManager->persistAll();

        $cart->setOrderId($orderItem->getUid());
        $cart->setOrderNumber($orderItem->getOrderNumber());
    }
}
