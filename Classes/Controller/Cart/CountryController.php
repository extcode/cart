<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Cart;

use Psr\Http\Message\ResponseInterface;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
class CountryController extends ActionController
{
    public function updateAction(): ResponseInterface
    {
        //ToDo check country is allowed by TypoScript

        $this->cartUtility->updateCountry($this->settings['cart'], $this->configurations, $this->request);

        $this->restoreSession();

        $taxClasses = $this->parserUtility->parseTaxClasses($this->configurations, $this->cart->getBillingCountry());

        $this->cart->setTaxClasses($taxClasses);
        $this->cart->reCalc();

        $this->parseServicesAndAssignToView();

        $paymentId = $this->cart->getPayment()->getId();
        if ($this->payments[$paymentId]) {
            $payment = $this->payments[$paymentId];
            $this->cart->setPayment($payment);
        } else {
            foreach ($this->payments as $payment) {
                if ($payment->isPreset()) {
                    $this->cart->setPayment($payment);
                }
            }
        }
        $shippingId = $this->cart->getShipping()->getId();
        if ($this->shippings[$shippingId]) {
            $shipping = $this->shippings[$shippingId];
            $this->cart->setShipping($shipping);
        } else {
            foreach ($this->shippings as $shipping) {
                if ($shipping->isPreset()) {
                    $this->cart->setShipping($shipping);
                }
            }
        }

        $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);

        $this->cartUtility->updateService($this->cart, $this->configurations);

        $this->view->assign('cart', $this->cart);

        return $this->htmlResponse();
    }
}
