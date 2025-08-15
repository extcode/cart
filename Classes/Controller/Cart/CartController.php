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
use Extcode\Cart\Event\Cart\BeforeShowCartEvent;
use Extcode\Cart\Event\CheckProductAvailabilityEvent;
use Extcode\Cart\View\CartTemplateView;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;

class CartController extends ActionController
{
    public function __construct()
    {
        $this->defaultViewObjectName = CartTemplateView::class;
    }

    protected function initializeView(CartTemplateView $view): void
    {
        if ($this->request->getControllerActionName() !== 'show') {
            return;
        }

        if (!isset($this->settings['cart']['steps'])) {
            return;
        }

        $steps = (int)($this->settings['cart']['steps'] ?? 0);
        if ($steps > 1) {
            if ($this->request->hasArgument('step')) {
                $currentStep = (int)$this->request->getArgument('step') ?: 1;
            } else {
                $currentStep = 1;
            }

            if ($currentStep > $steps) {
                throw new \InvalidArgumentException();
            }
            $view->setStep($currentStep);

            if ($currentStep < $steps) {
                $view->assign('nextStep', $currentStep + 1);
            }
            if ($currentStep > 1) {
                $view->assign('previousStep', $currentStep - 1);
            }
        }
    }

    public function showAction(
        ?Item $orderItem = null,
        ?BillingAddress $billingAddress = null,
        ?ShippingAddress $shippingAddress = null
    ): ResponseInterface {
        $this->restoreSession();

        $extbaseAttribute = $this->request->getAttribute('extbase');

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

            if ($extbaseAttribute instanceof ExtbaseRequestParameters
                && $extbaseAttribute->getOriginalRequest()
                && $extbaseAttribute->getOriginalRequest()->hasArgument('orderItem')
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

        $beforeShowCartEvent = new BeforeShowCartEvent($this->cart, $orderItem, $billingAddress, $shippingAddress);
        $this->eventDispatcher->dispatch($beforeShowCartEvent);

        $orderItem = $beforeShowCartEvent->getOrderItem();
        $billingAddress = $beforeShowCartEvent->getBillingAddress();
        $shippingAddress = $beforeShowCartEvent->getShippingAddress();

        $this->parseServicesAndAssignToView();

        $this->view->assignMultiple(
            [
                'cart' => $this->cart,
                'orderItem' => $orderItem,
                'billingAddress' => $billingAddress,
                'shippingAddress' => $shippingAddress,
            ]
        );

        // Use Post/Redirect/Get pattern for multistep checkout

        $currentStep = null;
        if ($this->request->hasArgument('step')) {
            $currentStep = (int)$this->request->getArgument('step');
        }

        $currentStepHasError = false;
        if ($extbaseAttribute instanceof ExtbaseRequestParameters
            && $extbaseAttribute->getOriginalRequestMappingResults()->hasErrors()
        ) {
            $currentStepHasError = true;
        }

        if ($currentStep
            && $this->request->getMethod() === 'POST'
            && !$currentStepHasError
        ) {
            return $this->redirect('show', null, null, ['step' => $currentStep])->withStatus(303);
        }
        // Redirect to step 1 if cart is empty.
        if (count($this->cart->getProducts()) === 0 && $currentStep > 1) {
            return $this->redirect('show', null, null, ['step' => 1])->withStatus(303);
        }

        $this->dispatchModifyViewEvent();

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
            return $this->redirect('show');
        }

        $updateQuantities = $this->request->getArgument('quantities');

        if (!is_array($updateQuantities)) {
            return $this->redirect('show');
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

        $this->cartUtility->updateService($this->cart);

        $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);

        return $this->redirect('show');
    }
}
