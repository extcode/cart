<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;
use Extcode\Cart\Event\CheckProductAvailabilityEvent;
use http\Exception\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\View\ViewInterface;

class CartController extends ActionController
{
    protected function initializeView(ViewInterface $view): void
    {
        if ($this->request->getControllerActionName() !== 'show') {
            return;
        }

        if (!isset($this->settings['cart']['steps'])) {
            return;
        }

        $steps = (int)$this->settings['cart']['steps'];
        if ($steps > 1) {
            if ($this->request->hasArgument('step')) {
                $currentStep = (int)$this->request->getArgument('step') ?: 1;
            } else {
                $currentStep = 1;
            }

            if ($currentStep > $steps) {
                throw new InvalidArgumentException();
            }
            $view->setStep($currentStep);

            if ($currentStep < $steps) {
                $view->assign('nextStep', $currentStep+1);
            }
            if ($currentStep > 1) {
                $view->assign('previousStep', $currentStep-1);
            }
        }
    }

    public function showAction(
        Item $orderItem = null,
        BillingAddress $billingAddress = null,
        ShippingAddress $shippingAddress = null
    ): ResponseInterface {
        $this->restoreSession();

        if (!is_null($billingAddress)) {
            $this->sessionHandler->writeAddress('billing_address_' . $this->settings['cart']['pid'], $billingAddress);
        } else {
            $billingAddress = $this->sessionHandler->restoreAddress('billing_address_' . $this->settings['cart']['pid']);
            if ($billingAddress === null) {
                $billingAddress = GeneralUtility::makeInstance(BillingAddress::class);
            }
        }
        if (!is_null($shippingAddress)) {
            $this->sessionHandler->writeAddress('shipping_address_' . $this->settings['cart']['pid'], $shippingAddress);
        } else {
            $shippingAddress = $this->sessionHandler->restoreAddress('shipping_address_' . $this->settings['cart']['pid']);
            if ($shippingAddress === null) {
                $shippingAddress = GeneralUtility::makeInstance(ShippingAddress::class);
            }
        }

        if ($orderItem === null) {
            $orderItem = GeneralUtility::makeInstance(
                Item::class
            );

            $extbaseAttribute = $this->request->getAttribute('extbase');
            if ($extbaseAttribute instanceof ExtbaseRequestParameters &&
                $extbaseAttribute->getOriginalRequest() &&
                $extbaseAttribute->getOriginalRequest()->hasArgument('orderItem')
            ) {
                $originalRequestOrderItem = $extbaseAttribute->getOriginalRequest()->getArgument('orderItem');

                if (isset($originalRequestOrderItem['shippingSameAsBilling'])) {
                    $this->cart->setShippingSameAsBilling((bool)$originalRequestOrderItem['shippingSameAsBilling']);
                    $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);

                }
            }
        } else {
            $this->cart->setShippingSameAsBilling($orderItem->isShippingSameAsBilling());
            $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);
        }
        if (
            isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['showCartActionAfterCartWasLoaded']) &&
            !empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['showCartActionAfterCartWasLoaded'])
        ) {
            DebuggerUtility::var_dump("hi");
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['showCartActionAfterCartWasLoaded'] as $funcRef) {
                if ($funcRef) {
                    $params = [
                        'request' => $this->request,
                        'settings' => $this->settings,
                        'cart' => &$this->cart,
                        'orderItem' => &$orderItem,
                        'billingAddress' => &$billingAddress,
                        'shippingAddress' => &$shippingAddress,
                    ];

                    GeneralUtility::callUserFunction($funcRef, $params, $this);
                }
            }
        }

        $this->view->assign('cart', $this->cart);

        $this->view->assignMultiple(
            [
                'orderItem' => $orderItem,
                'billingAddress' => $billingAddress,
                'shippingAddress' => $shippingAddress,
            ]
        );

        return $this->htmlResponse();
    }

    public function clearAction(): ResponseInterface
    {
        $this->cart = $this->cartUtility->getNewCart($this->configurations);

        $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);

        return $this->redirect('show');
    }

    public function updateAction(): ResponseInterface
    {
        if (!$this->request->hasArgument('quantities')) {
            $this->redirect('show');
        }

        $updateQuantities = $this->request->getArgument('quantities');

        if (!is_array($updateQuantities)) {
            $this->redirect('show');
        }

        $this->restoreSession();

        foreach ($updateQuantities as $productId => $quantity) {
            $cartProduct = $this->cart->getProductById($productId);
            if ($cartProduct) {
                $checkAvailabilityEvent = new CheckProductAvailabilityEvent($this->cart, $cartProduct, $quantity);
                $this->eventDispatcher->dispatch($checkAvailabilityEvent);
                if ($checkAvailabilityEvent->isAvailable()) {
                    if (is_array($quantity)) {
                        $cartProduct->changeQuantities($quantity);
                    } else {
                        $cartProduct->changeQuantity((int)$quantity);
                    }
                } else {
                    foreach ($checkAvailabilityEvent->getMessages() as $message) {
                        $message = $message->jsonSerialize();
                        $this->addFlashMessage(
                            $message['message'],
                            $message['title'],
                            $message['severity'],
                            true
                        );
                    }
                }
            }
        }
        $this->cart->reCalc();
        DebuggerUtility::var_dump($this->cart);

        $this->cartUtility->updateService($this->cart, $this->configurations);

        $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);

        return $this->redirect('show');
    }
}
