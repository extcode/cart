<?php

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Finisher\Cart\AddToCartFinisherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class StockUtility
{
    /**
     * Persistence Manager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager;

    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
     */
    public function injectPersistenceManager(
        \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
    ) {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\Web\Request
     * @param \Extcode\Cart\Domain\Model\Cart\Product $cartProduct
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @param string $mode
     */
    public function checkAvailability(
        \TYPO3\CMS\Extbase\Mvc\Web\Request $request,
        \Extcode\Cart\Domain\Model\Cart\Product $cartProduct,
        \Extcode\Cart\Domain\Model\Cart\Cart $cart,
        string $mode
    ) {
        $productType = $cartProduct->getProductType();
        if (empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart'][$productType]['Cart']['AddToCartFinisher'])) {
            // TODO: throw own exception
            throw new \Exception('Hook is not configured for this product type!');
        }

        $className = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart'][$productType]['Cart']['AddToCartFinisher'];

        $hookObject = GeneralUtility::makeInstance($className);
        if (!$hookObject instanceof AddToCartFinisherInterface) {
            throw new \UnexpectedValueException($className . ' must implement interface ' . AddToCartFinisherInterface::class, 123);
        }

        return $hookObject->checkAvailability($request, $cartProduct, $cart, $mode);
    }

    /**
     * Check Stock
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function checkStock(
        \Extcode\Cart\Domain\Model\Cart\Cart $cart
    ) {
        $this->beforeCheckStock($cart);

        /** @var \Extcode\Cart\Domain\Model\Cart\Product $cartProduct */
        foreach ($cart->getProducts() as $cartProduct) {
            $data = [
                'cartProduct' => $cartProduct,
            ];

            $signalSlotDispatcher = GeneralUtility::makeInstance(
                \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
            );
            $signalSlotDispatcher->dispatch(
                __CLASS__,
                __FUNCTION__,
                [$data]
            );
        }

        $this->afterCheckStock($cart);
    }

    /**
     * Before Check Stock
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function beforeCheckStock(
        \Extcode\Cart\Domain\Model\Cart\Cart $cart
    ) {
        $data = [
            'cart' => $cart,
        ];

        $signalSlotDispatcher = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            [$data]
        );
    }

    /**
     * After Check Stock
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function afterCheckStock(
        \Extcode\Cart\Domain\Model\Cart\Cart $cart
    ) {
        $data = [
            'cart' => $cart,
        ];

        $signalSlotDispatcher = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            [$data]
        );
    }

    /**
     * Handle Stock
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @param array $pluginSettings
     */
    public function handleStock(
        \Extcode\Cart\Domain\Model\Cart\Cart $cart
    ) {
        $this->beforeHandleStock($cart);

        /** @var \Extcode\Cart\Domain\Model\Cart\Product $cartProduct */
        foreach ($cart->getProducts() as $cartProduct) {
            $data = [
                'cartProduct' => $cartProduct
            ];

            $signalSlotDispatcher = GeneralUtility::makeInstance(
                \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
            );
            $signalSlotDispatcher->dispatch(
                __CLASS__,
                __FUNCTION__,
                [$data]
            );
        }

        $this->persistenceManager->persistAll();

        $this->afterHandleStock($cart);
    }

    /**
     * Before Handle Stock
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function beforeHandleStock(
        \Extcode\Cart\Domain\Model\Cart\Cart $cart
    ) {
        $data = [
            'cart' => $cart,
        ];

        $signalSlotDispatcher = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            [$data]
        );
    }

    /**
     * After Handle Stock
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function afterHandleStock(
        \Extcode\Cart\Domain\Model\Cart\Cart $cart
    ) {
        $data = [
            'cart' => $cart,
        ];

        $signalSlotDispatcher = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            [$data]
        );
    }
}
