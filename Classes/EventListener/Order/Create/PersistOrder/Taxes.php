<?php
namespace Extcode\Cart\EventListener\Order\Create\PersistOrder;

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\Tax;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Extcode\Cart\Domain\Repository\Order\TaxRepository;
use Extcode\Cart\Event\Order\PersistOrderEventInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class Taxes
{
    protected Cart $cart;
    protected Item $orderItem;
    protected int $storagePid;
    protected PersistOrderEventInterface $event;

    public function __construct(
        private readonly PersistenceManager $persistenceManager,
        private readonly ItemRepository $itemRepository,
        private readonly TaxRepository $taxRepository,
    ) {
    }

    public function __invoke(PersistOrderEventInterface $event): void
    {
        $this->event = $event;
        $this->cart = $event->getCart();
        $this->storagePid = $event->getStoragePid();
        $this->orderItem = $event->getOrderItem();

        $this->addTaxes('TotalTax');
        $this->addTaxes('Tax');
    }

    /**
     * Adds a Taxes To Order
     *
     * @param string $type Type of the Tax
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws \Exception
     */
    protected function addTaxes(string $type = 'Tax'): void
    {
        $cartTaxes = call_user_func([$this->cart, 'get' . $type . 'es']);
        foreach ($cartTaxes as $cartTaxKey => $cartTax) {
            /**
             * Order Tax
             * @var \Extcode\Cart\Domain\Model\Order\Tax $orderTax
             */
            $orderTax = GeneralUtility::makeInstance(
                Tax::class,
                $cartTax,
                // get OrderTaxClass by Value from CartTaxclass (since $cartTaxKey does not match for Order)
                $this->getOrderTaxClassByValue((int)$this->cart->getTaxClass($cartTaxKey)->getValue())
            );
            $orderTax->setPid($this->storagePid);

            $this->taxRepository->add($orderTax);

            call_user_func([$this->orderItem, 'add' . $type], $orderTax);
        }
        $this->itemRepository->update($this->orderItem);
        $this->persistenceManager->persistAll();
    }

    protected function getOrderTaxClassByValue(int $value): \Extcode\Cart\Domain\Model\Order\TaxClass
    {
        foreach ($this->event->getTaxClasses() as $taxClass) {
            if ((int)$taxClass->getValue() === $value) {
                return $taxClass;
            }
        }
        throw new \Exception('Tax class not found', 1717086058);
    }
}
