<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create\PersistOrder;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\AdditionalDataInterface;
use Extcode\Cart\Domain\Model\Cart\BeVariantInterface;
use Extcode\Cart\Domain\Model\Cart\FeVariantInterface;
use Extcode\Cart\Domain\Model\Cart\ProductInterface;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\Product;
use Extcode\Cart\Domain\Model\Order\ProductAdditional;
use Extcode\Cart\Domain\Repository\Order\ProductAdditionalRepository;
use Extcode\Cart\Domain\Repository\Order\ProductRepository;
use Extcode\Cart\Event\Order\PersistOrderEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class Products
{
    private Item $orderItem;

    protected array $taxClasses;

    private int $storagePid;

    public function __construct(
        private readonly PersistenceManager $persistenceManager,
        private readonly ProductRepository $productRepository,
        private readonly ProductAdditionalRepository $productAdditionalRepository
    ) {}

    public function __invoke(PersistOrderEvent $event): void
    {
        $cart = $event->getCart();
        $this->orderItem = $event->getOrderItem();
        $this->storagePid = $event->getStoragePid();
        $this->taxClasses = $event->getTaxClasses();

        foreach ($cart->getProducts() as $cartProduct) {
            if ($cartProduct->getBeVariants()) {
                $this->addProductVariants($cartProduct);
            } else {
                $this->addProduct($cartProduct);
            }
        }

        $this->persistenceManager->persistAll();
    }

    protected function addProduct(ProductInterface $cartProduct): void
    {
        $orderProduct = GeneralUtility::makeInstance(
            Product::class
        );
        $orderProduct->setSku($cartProduct->getSku());
        $orderProduct->setTitle($cartProduct->getTitle());
        $orderProduct->setCount($cartProduct->getQuantity());

        $orderProduct->setProductType($cartProduct->getProductType());
        $orderProduct->setProductId($cartProduct->getProductId());
        $orderProduct->setPrice($cartProduct->getTranslatedPrice());
        $orderProduct->setDiscount($cartProduct->getDiscount());
        $orderProduct->setGross($cartProduct->getGross());
        $orderProduct->setNet($cartProduct->getNet());
        $orderProduct->setTaxClass($this->taxClasses[$cartProduct->getTaxClass()->getId()]);
        $orderProduct->setTax($cartProduct->getTax());
        if ($cartProduct instanceof AdditionalDataInterface) {
            $orderProduct->setAdditional($cartProduct->getAdditionals());
        }

        $orderProduct->setPid($this->storagePid);

        $this->productRepository->add($orderProduct);

        $this->orderItem->addProduct($orderProduct);

        $this->addFeVariants($orderProduct, $cartProduct->getFeVariant());
    }

    protected function addProductVariants(ProductInterface $product): void
    {
        foreach ($product->getBeVariants() as $variant) {
            if ($variant->getBeVariants()) {
                $this->addVariantsOfVariant($variant, 1);
            } else {
                $this->addBeVariant($variant, 1);
            }
        }
    }

    protected function addFeVariants(
        Product $product,
        ?FeVariantInterface $feVariant = null
    ): void {
        if ($feVariant) {
            $feVariantsData = $feVariant->getVariantData();
            if ($feVariantsData) {
                foreach ($feVariantsData as $feVariant) {
                    $this->addProductAdditional('FeVariant', $product, $feVariant);
                }
            }
        }
    }

    protected function addProductAdditional(
        string $productAdditionalType,
        Product $product,
        array $feVariant
    ): void {
        $productAdditional = GeneralUtility::makeInstance(
            ProductAdditional::class,
            $productAdditionalType,
            $feVariant['sku'],
            $feVariant['value'],
            $feVariant['title']
        );
        $productAdditional->setPid($this->storagePid);

        $this->productAdditionalRepository->add($productAdditional);

        $product->addProductAdditional($productAdditional);
    }

    protected function addVariantsOfVariant(BeVariantInterface $variant, int $level): void
    {
        $level += 1;

        foreach ($variant->getBeVariants() as $variantInner) {
            if ($variantInner->getBeVariants()) {
                $this->addVariantsOfVariant($variantInner, $level);
            } else {
                $this->addBeVariant($variantInner, $level);
            }
        }
    }

    protected function addBeVariant(BeVariantInterface $variant, int $level): void
    {
        $cartProduct = $variant->getProduct();

        $orderProduct = GeneralUtility::makeInstance(
            Product::class
        );
        $orderProduct->setSku($variant->getCompleteSku());
        $orderProduct->setTitle($variant->getCompleteTitle());
        $orderProduct->setCount($variant->getQuantity());

        $orderProduct->setProductType($cartProduct->getProductType());
        $orderProduct->setPrice($variant->getPriceCalculated());
        $orderProduct->setDiscount($variant->getDiscount());
        $orderProduct->setGross($variant->getGross());
        $orderProduct->setNet($variant->getNet());
        $orderProduct->setTaxClass($this->taxClasses[$variant->getTaxClass()->getId()]);
        $orderProduct->setTax($variant->getTax());
        if ($cartProduct instanceof AdditionalDataInterface) {
            $orderProduct->setAdditional($cartProduct->getAdditionals());
        }

        if (!$orderProduct->_isDirty()) {
            $this->productRepository->add($orderProduct);
        }

        $this->addFeVariants($orderProduct, $cartProduct->getFeVariant());

        $variantInner = $variant;
        for ($count = $level; $count > 0; $count--) {
            $orderProductAdditional = GeneralUtility::makeInstance(
                ProductAdditional::class,
                'variant_' . $count,
                $variantInner->getCompleteSku(),
                $variantInner->getTitle()
            );
            if ($variantInner instanceof AdditionalDataInterface) {
                $orderProductAdditional->setAdditional($variantInner->getAdditionals());
            }
            $orderProductAdditional->setPid($this->storagePid);

            $this->productAdditionalRepository->add($orderProductAdditional);

            $orderProduct->addProductAdditional($orderProductAdditional);

            $variantInner = $variantInner->getParent();
        }
        unset($variantInner);

        $orderProduct->setPid($this->storagePid);
        $this->productRepository->add($orderProduct);

        $this->orderItem->addProduct($orderProduct);
    }
}
