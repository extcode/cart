<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Product;
use Extcode\Cart\Event\CheckProductAvailabilityEvent;
use Extcode\Cart\Event\RetrieveProductsFromRequestEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ProductController extends ActionController
{
    public const AJAX_CART_TYPE_NUM = '2278001';

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher = null
    ) {
        if ($eventDispatcher !== null) {
            $this->eventDispatcher = $eventDispatcher;
        }
    }

    public function addAction(): ResponseInterface
    {
        if (!$this->request->hasArgument('productType')) {
            // TODO: add own Exception
            throw new \Exception('productType is needed');
        }

        $this->restoreSession();

        $event = new RetrieveProductsFromRequestEvent($this->request, $this->cart);

        $this->eventDispatcher->dispatch($event);

        $errors = $event->getErrors();

        $cartProducts = $event->getProducts();

        foreach ($cartProducts as $cartProduct) {
            $checkAvailabilityEvent = new CheckProductAvailabilityEvent($this->cart, $cartProduct, $cartProduct->getQuantity(), 'add');
            $this->eventDispatcher->dispatch($checkAvailabilityEvent);

            if (!$checkAvailabilityEvent->isAvailable()) {
                $errors = array_merge($errors, $checkAvailabilityEvent->getMessages());
            }
        }

        $messageBody = '';
        $messageTitle = '';
        $severity = ContextualFeedbackSeverity::OK;

        if (!empty($errors)) {
            foreach ($errors as $error) {
                if (!($error instanceof AbstractMessage)) {
                    continue;
                }
                $message = $error->jsonSerialize();
                if ($message['severity'] >= (int)$severity) {
                    $severity = $message['severity'];
                    $messageBody = $message['message'];
                    $messageTitle = $message['title'];
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
                    'severity' => $severity,
                ];

                return $this->jsonResponse(json_encode($response));
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

        $this->cartUtility->updateService($this->cart, $this->configurations);

        $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);

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
                'severity' => $severity,
            ];

            return $this->jsonResponse(json_encode($response));
        }

        $this->addFlashMessage(
            $messageBody,
            $messageTitle,
            $severity,
            true
        );

        return $this->redirect('show', 'Cart\Cart');
    }

    public function removeAction(): ResponseInterface
    {
        if ($this->request->hasArgument('product')) {
            $this->restoreSession();
            $productArgument = $this->request->getArgument('product');

            if ($this->request->hasArgument('variant')) {
                $variantArgument = $this->request->getArgument('variant');

                $this->cart->removeProductByIds([$productArgument => [$variantArgument => 1]]);
            } else {
                $this->cart->removeProductById($productArgument);
            }

            $this->cartUtility->updateService($this->cart, $this->configurations);

            $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);
        }

        return $this->redirect('show', 'Cart\Cart');
    }

    /**
     * returns list of changed products
     */
    protected function getChangedProducts(array $products): array
    {
        $productsChanged = [];

        foreach ($products as $product) {
            if ($product instanceof Product) {
                $productChanged = $this->cart->getProductById($product->getId());
                $productsChanged[$product->getId()] = $productChanged->toArray();
            }
        }
        return $productsChanged;
    }

    protected function addProductsToCart(array $products): int
    {
        $quantity = 0;

        foreach ($products as $product) {
            if ($product instanceof Product) {
                $quantity += $product->getQuantity();
                $this->cart->addProduct($product);
            }
        }
        return $quantity;
    }
}
