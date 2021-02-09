<?php

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\CheckProductAvailabilityEvent;
use Extcode\Cart\Event\RetrieveProductsFromRequestEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ProductController extends ActionController
{
    const AJAX_CART_TYPE_NUM = '2278001';

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher = null)
    {
        if ($eventDispatcher !== null) {
            $this->eventDispatcher = $eventDispatcher;
        }
    }

    /**
     * Action Add
     *
     * @return string
     */
    public function addAction()
    {
        if (!$this->request->hasArgument('productType')) {
            // TODO: add own Exception
            throw new \Exception('productType is needed');
        }

        $this->cart = $this->cartUtility->getCartFromSession($this->pluginSettings);

        $event = new RetrieveProductsFromRequestEvent($this->request, $this->cart);

        $this->eventDispatcher->dispatch($event);

        $errors = [];

        $cartProducts = $event->getProducts();

        foreach ($cartProducts as $cartProductKey => $cartProduct) {
            $checkAvailabilityEvent = new CheckProductAvailabilityEvent($this->cart, $cartProduct, $cartProduct->getQuantity(), 'add');
            $this->eventDispatcher->dispatch($checkAvailabilityEvent);

            if (!$checkAvailabilityEvent->isAvailable()) {
                $errors = array_merge($errors, $checkAvailabilityEvent->getMessages());
            }
        }

        $messageBody = '';
        $messageTitle = '';
        $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::OK;

        if (!empty($errors)) {
            foreach ($errors as $error) {
                if ($error->getSeverity() >= $severity) {
                    $severity = $error->getSeverity();
                    $messageBody = $error->getMessage();
                    $messageTitle = $error->getTitle();
                }
            }

            $pageType = $GLOBALS['TYPO3_REQUEST']->getAttribute('routing')->getPageType();
            if ($pageType === self::AJAX_CART_TYPE_NUM) {
                $response = [
                    'status' => '412',
                    'count' => $this->cart->getCount(),
                    'net' => $this->cart->getNet(),
                    'gross' => $this->cart->getGross(),
                    'messageBody' => $messageBody,
                    'messageTitle' => $messageTitle,
                    'severity' => $severity
                ];

                return json_encode($response);
            }

            $this->addFlashMessage(
                $messageBody,
                $messageTitle,
                $severity,
                true
            );

            $this->redirect('show', 'Cart\Cart');
        }

        $quantity = $this->addProductsToCart($cartProducts);

        $this->cartUtility->updateService($this->cart, $this->pluginSettings);

        $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);

        $messageBody = LocalizationUtility::translate(
            'tx_cart.success.stock_handling.add.' . ($quantity == 1 ? 'one' : 'more'),
            'Cart',
            [$quantity]
        );

        $pageType = $GLOBALS['TYPO3_REQUEST']->getAttribute('routing')->getPageType();
        if ($pageType === self::AJAX_CART_TYPE_NUM) {
            $productsChanged = $this->getChangedProducts($cartProducts);

            $response = [
                'status' => '200',
                'added' => $quantity,
                'count' => $this->cart->getCount(),
                'net' => $this->cart->getNet(),
                'gross' => $this->cart->getGross(),
                'productsChanged' => $productsChanged,
                'messageBody' => $messageBody,
                'messageTitle' => $messageTitle,
                'severity' => $severity
            ];

            return json_encode($response);
        }

        $this->addFlashMessage(
            $messageBody,
            $messageTitle,
            $severity,
            true
        );

        $this->redirect('show', 'Cart\Cart');
    }

    /**
     * Action remove
     */
    public function removeAction()
    {
        if ($this->request->hasArgument('product')) {
            $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);
            $productArgument = $this->request->getArgument('product');
            if (is_array($productArgument)) {
                $this->cart->removeProductByIds($productArgument);
            } else {
                $this->cart->removeProductById($productArgument);
            }

            $this->cartUtility->updateService($this->cart, $this->pluginSettings);

            $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);
        }
        $this->redirect('show', 'Cart\Cart');
    }

    /**
     * returns list of changed products
     *
     * @param $products
     *
     * @return array
     */
    protected function getChangedProducts($products)
    {
        $productsChanged = [];

        foreach ($products as $product) {
            if ($product instanceof \Extcode\Cart\Domain\Model\Cart\Product) {
                $productChanged = $this->cart->getProduct($product->getId());
                $productsChanged[$product->getId()] = $productChanged->toArray();
            }
        }
        return $productsChanged;
    }

    /**
     * @param array $products
     * @return int
     */
    protected function addProductsToCart($products)
    {
        $quantity = 0;

        foreach ($products as $product) {
            if ($product instanceof \Extcode\Cart\Domain\Model\Cart\Product) {
                $quantity += $product->getQuantity();
                $this->cart->addProduct($product);
            }
        }
        return $quantity;
    }
}
