<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class Service implements ServiceInterface
{
    protected Cart $cart;

    protected int $fallBackId;

    protected bool $preset = false;

    public function __construct(
        protected int $id,
        protected array $config = []
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setCart(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getStatus(): string
    {
        return $this->config['status'] ?: 'open';
    }

    public function getProcessOrderCreateEvent(): string
    {
        return $this->config['processOrderCreateEvent'] ?: '';
    }

    public function getProvider(): string
    {
        return $this->config['provider']?? '';
    }

    public function getName(): string
    {
        return $this->config['title'];
    }

    public function getGross(): float
    {
        $extra = $this->getExtra();

        $extraGross = $this->cart->translatePrice($extra->getGross());

        if ($extra->getExtraType() === 'each') {
            return $this->cart->getCount() * $extraGross;
        }

        return $extraGross;
    }

    public function getNet(): float
    {
        $extra = $this->getExtra();

        $extraNet = $this->cart->translatePrice($extra->getNet());

        if ($extra->getExtraType() === 'each') {
            return $this->cart->getCount() * $extraNet;
        }

        return $extraNet;
    }

    public function getTax(): float
    {
        $extra = $this->getExtra();

        $extraTax = $extra->getTax();

        $taxValue = $this->cart->translatePrice($extraTax['tax']);

        if ($extra->getExtraType() === 'each') {
            return $this->cart->getCount() * $taxValue;
        }

        return $taxValue;
    }

    public function getTaxes(): array
    {
        if ($this->getTaxClass()->getId() > 0) {
            return [
                [
                    'taxClassId' => $this->getTaxClass()->getId(),
                    'tax' => $this->getTax(),
                ],
            ];
        }

        $taxes = [];

        if ($this->getTaxClass()->getId() === -2) {
            $extra = $this->getExtra();

            $usedTaxClasses = [];

            foreach ($this->cart->getProducts() as $product) {
                if (!in_array($product->getTaxClass(), $usedTaxClasses, true)) {
                    $usedTaxClasses[] = $product->getTaxClass();
                }
            }

            foreach ($usedTaxClasses as $taxClass) {
                $extraTax = $extra->getTaxForTaxClass($taxClass);

                $taxValue = $this->cart->translatePrice($extraTax);

                if ($extra->getExtraType() === 'each') {
                    $taxValue = $this->cart->getCount() * $taxValue;
                }

                $taxes[] = [
                    'taxClassId' => $taxClass->getId(),
                    'tax' => $taxValue,
                ];
            }
        }

        return $taxes;
    }

    public function getTaxClass(): TaxClass
    {
        $taxClass = null;

        if ((int)$this->config['taxClassId'] > 0) {
            return $this->cart->getTaxClass((int)$this->config['taxClassId']);
        }

        if ((int)$this->config['taxClassId'] === -1) {
            // assign lowest TaxClass
            foreach ($this->cart->getTaxClasses() as $cartTaxClass) {
                if ($taxClass === null || $taxClass->getCalc() > $cartTaxClass->getCalc()) {
                    $taxClass = $cartTaxClass;
                }
            }
            // assign highest used TaxClass
            foreach ($this->cart->getProducts() as $product) {
                if ($product->getTaxClass()->getCalc() > $taxClass->getCalc()) {
                    $taxClass = $product->getTaxClass();
                }
            }
        }

        if ((int)$this->config['taxClassId'] === -2) {
            $taxClass = new TaxClass(
                $this->id = -2,
                '0',
                0.0,
                'internal'
            );
        }

        return $taxClass;
    }

    public function isPreset(): bool
    {
        return $this->preset;
    }

    public function setPreset(bool $preset): void
    {
        $this->preset = $preset;
    }

    public function getFallBackId(): ?int
    {
        if (!isset($this->config['fallBackId'])) {
            return null;
        }
        return (int)$this->config['fallBackId'];
    }

    public function isAvailable(): bool
    {
        if (isset($this->config['available'])) {
            $availableFrom = $this->config['available']['from'];
            if (isset($availableFrom) && $this->cart->getGross() < (float)$availableFrom) {
                return false;
            }
            $availableUntil = $this->config['available']['until'];
            if (isset($availableUntil) && $this->cart->getGross() > (float)$availableUntil) {
                return false;
            }
        }

        return true;
    }

    public function isFree(): bool
    {
        if (isset($this->config['free']['from']) || isset($this->config['free']['until'])) {
            $freeFrom = $this->config['free']['from'];
            if (isset($freeFrom) && $this->cart->getGross() < (float)$freeFrom) {
                return false;
            }

            $freeUntil = $this->config['free']['until'];
            if (isset($freeUntil) && $this->cart->getGross() > (float)$freeUntil) {
                return false;
            }

            return true;
        }

        return false;
    }

    protected function getExtra(): Extra
    {
        if ($this->isFree()) {
            return new Extra(
                0,
                0,
                0.0,
                $this->getTaxClass(),
                $this->cart->isNetCart(),
                '',
                $this
            );
        }

        if (is_array($this->config['extra']) && isset($this->config['extra']['_typoScriptNodeValue'])) {
            $extraType = $this->config['extra']['_typoScriptNodeValue'];

            if ($extraType === 'each') {
                return new Extra(
                    0,
                    0,
                    (float)$this->config['extra']['extra'],
                    $this->getTaxClass(),
                    $this->cart->isNetCart(),
                    $extraType,
                    $this
                );
            }

            $conditionValue = $this->getConditionValueFromCart($extraType);

            $extra = null;

            foreach ($this->config['extra'] as $extraKey => $extraValue) {
                if (is_array($extraValue) && ((float)$extraValue['value'] <= (float)$conditionValue)) {
                    $extra = new Extra(
                        $extraKey,
                        (float)$extraValue['value'],
                        (float)$extraValue['extra'],
                        $this->getTaxClass(),
                        $this->cart->isNetCart(),
                        $extraType,
                        $this
                    );
                }
            }

            return $extra;
        }

        return new Extra(
            0,
            0,
            (float)$this->config['extra'],
            $this->getTaxClass(),
            $this->cart->isNetCart(),
            '',
            $this
        );
    }

    /**
     * @return float|int|null
     */
    protected function getConditionValueFromCart(string $extraType)
    {
        switch ($extraType) {
            case 'by_price':
                return $this->cart->getGross();
            case 'by_price_of_physical_products':
                return $this->getPriceOfPhysicalProducts();
            case 'by_quantity':
            case 'by_number_of_physical_products':
                return $this->cart->getCountPhysicalProducts();
            case 'by_number_of_virtual_products':
                return $this->cart->getCountVirtualProducts();
            case 'by_number_of_all_products':
                return $this->cart->getCount();
            case 'by_service_attribute_1_sum':
                return $this->cart->getSumServiceAttribute1();
            case 'by_service_attribute_1_max':
                return $this->cart->getMaxServiceAttribute1();
            case 'by_service_attribute_2_sum':
                return $this->cart->getSumServiceAttribute2();
            case 'by_service_attribute_2_max':
                return $this->cart->getMaxServiceAttribute2();
            case 'by_service_attribute_3_sum':
                return $this->cart->getSumServiceAttribute3();
            case 'by_service_attribute_3_max':
                return $this->cart->getMaxServiceAttribute3();
            default:
                return null;
        }
    }

    protected function getPriceOfPhysicalProducts(): float
    {
        $calculatedGross = 0.0;

        if ($this->cart->getProducts()) {
            foreach ($this->cart->getProducts() as $product) {
                if (!$product->isVirtualProduct()) {
                    $calculatedGross += $product->getGross();
                }
            }
        }

        return $calculatedGross;
    }
}
