<?php

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Finisher\Cart\AddToCartFinisherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProductController extends ActionController
{
    const AJAX_CART_TYPE_NUM = '2278001';

    /**
     * Stock Utility
     *
     * @var \Extcode\Cart\Utility\StockUtility
     */
    protected $stockUtility;

    /**
     * GpValues
     *
     * @var array
     */
    protected $gpValues = [];

    /**
     * TaxClasses
     *
     * @var array
     */
    protected $taxClasses = [];

    /**
     * @param \Extcode\Cart\Utility\StockUtility $stockUtility
     */
    public function injectStockUtility(
        \Extcode\Cart\Utility\StockUtility $stockUtility
    ) {
        $this->stockUtility = $stockUtility;
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

        $productType = $this->request->getArgument('productType');

        $this->cart = $this->cartUtility->getCartFromSession($this->pluginSettings);

        if (empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart'][$productType]['Cart']['AddToCartFinisher'])) {
            // TODO: throw own exception
            throw new \Exception('Hook is not configured for this product type!');
        }

        $className = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart'][$productType]['Cart']['AddToCartFinisher'];

        $hookObject = GeneralUtility::makeInstance($className);
        if (!$hookObject instanceof AddToCartFinisherInterface) {
            throw new \UnexpectedValueException($className . ' must implement interface ' . AddToCartFinisherInterface::class, 123);
        }

        list($errors, $cartProducts) = $hookObject->getProductFromRequest(
            $this->request,
            $this->cart
        );

        $errors = [];

        foreach ($cartProducts as $cartProductKey => $cartProduct) {
            $availabilityResponse = $this->stockUtility->checkAvailability(
                $this->request,
                $cartProduct,
                $this->cart,
                'add'
            );

            if (!$availabilityResponse->isAvailable()) {
                $errors = array_merge($errors, $availabilityResponse->getMessages());
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
            } else {
                $this->addFlashMessage(
                    $messageBody,
                    $messageTitle,
                    $severity,
                    true
                );

                $this->redirect('show', 'Cart\Cart');
            }
        }

        $quantity = $this->addProductsToCart($cartProducts);

        $this->cartUtility->updateService($this->cart, $this->pluginSettings);

        $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);

        $messageBody = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
            'tx_cart.success.stock_handling.add.' . ($quantity == 1 ? 'one' : 'more'),
            'cart',
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
