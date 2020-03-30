<?php
declare(strict_types = 1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class Service implements ServiceInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var float
     */
    protected $gross;

    /**
     * @var float
     */
    protected $net;

    /**
     * @var float
     */
    protected $tax;

    /**
     * @var bool
     */
    protected $preset = false;

    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(
        \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param int $id
     * @param array $config
     */
    public function __construct(
        int $id,
        array $config
    ) {
        $this->id = $id;
        $this->config = $config;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param Cart $cart
     *
     * @return Service
     */
    public function setCart(Cart $cart): self
    {
        $this->cart = $cart;
        $this->calcAll();

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->config['status'] ?: 'open';
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->config['provider'] ?: '';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->config['title'];
    }

    /**
     * @return float
     */
    public function getGross(): float
    {
        return $this->gross;
    }

    /**
     * @return float
     */
    public function getNet(): float
    {
        return $this->net;
    }

    /**
     * @return float
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    /**
     * @return TaxClass
     */
    public function getTaxClass(): TaxClass
    {
        return $this->cart->getTaxClass($this->config['taxClassId']);
    }

    /**
     * @return bool
     */
    public function isPreset(): bool
    {
        return $this->preset;
    }

    /**
     * @param bool $preset
     */
    public function setPreset(bool $preset): void
    {
        $this->preset = $preset;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        if ($this->config['available']) {
            $availableFrom = $this->config['available']['from'];
            if (isset($availableFrom) && $this->cart->getGross() < (float)$availableFrom) {
                return false;
            }
            $availableUntil = $this->config['available']['until'];
            if (isset($availableUntil) && $this->cart->getGross() < (float)$availableUntil) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
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

    protected function calcAll()
    {
        $this->calcGross();
        $this->calcTax();
        $this->calcNet();
    }

    /**
     * @return \Extcode\Cart\Domain\Model\Cart\Extra
     */
    protected function getExtra()
    {
        if ($this->isFree()) {
            return $this->objectManager->get(
                \Extcode\Cart\Domain\Model\Cart\Extra::class,
                0,
                0,
                0.0,
                $this->cart->getTaxClass($this->config['taxClassId']),
                $this->cart->getIsNetCart()
            );
        }

        if (is_array($this->config['extra']) && isset($this->config['extra']['_typoScriptNodeValue'])) {
            $extraType = $this->config['extra']['_typoScriptNodeValue'];

            if ($extraType === 'each') {
                return $this->objectManager->get(
                    \Extcode\Cart\Domain\Model\Cart\Extra::class,
                    0,
                    0,
                    (float)$this->config['extra']['extra'],
                    $this->cart->getTaxClass($this->config['taxClassId']),
                    $this->cart->getIsNetCart(),
                    $extraType
                );
            }

            $conditionValue = $this->getConditionValueFromCart($extraType);

            foreach ($this->config['extra'] as $extraKey => $extraValue) {
                if (is_array($extraValue) && ((float)$extraValue['value'] <= (float)$conditionValue)) {
                    $extra = $this->objectManager->get(
                        \Extcode\Cart\Domain\Model\Cart\Extra::class,
                        $extraKey,
                        (float)$extraValue['value'],
                        (float)$extraValue['extra'],
                        $this->cart->getTaxClass($this->config['taxClassId']),
                        $this->cart->getIsNetCart(),
                        $extraType
                    );
                }
            }

            return $extra;
        }

        return $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Cart\Extra::class,
            0,
            0,
            (float)$this->config['extra'],
            $this->cart->getTaxClass($this->config['taxClassId']),
            $this->cart->getIsNetCart()
        );
    }

    protected function calcGross()
    {
        $extra = $this->getExtra();

        $gross = $this->cart->translatePrice($extra->getGross());

        if ($extra->getExtraType() === 'each') {
            $gross = $this->cart->getCount() * $gross;
        }

        $this->gross = $gross;
    }

    protected function calcNet()
    {
        $extra = $this->getExtra();

        $net = $this->cart->translatePrice($extra->getNet());

        if ($extra->getExtraType() === 'each') {
            $net = $this->cart->getCount() * $net;
        }

        $this->net = $net;
    }

    protected function calcTax()
    {
        $extra = $this->getExtra();

        $tax = $extra->getTax();

        $taxValue = $this->cart->translatePrice($tax['tax']);

        if ($extra->getExtraType() === 'each') {
            $taxValue = $this->cart->getCount() * $taxValue;
        }

        $this->tax = $taxValue;
    }

    /**
     * @param string $extraType
     *
     * @return float|int|null
     */
    protected function getConditionValueFromCart(string $extraType)
    {
        switch ($extraType) {
            case 'by_price':
                return $this->cart->getGross();
                break;
            case 'by_price_of_physical_products':
                return $this->getPriceOfPhysicalProducts();
                break;
            case 'by_quantity':
            case 'by_number_of_physical_products':
                return $this->cart->getCountPhysicalProducts();
                break;
            case 'by_number_of_virtual_products':
                return $this->cart->getCountVirtualProducts();
                break;
            case 'by_number_of_all_products':
                return $this->cart->getCount();
                break;
            case 'by_service_attribute_1_sum':
                return $this->cart->getSumServiceAttribute1();
                break;
            case 'by_service_attribute_1_max':
                return $this->cart->getMaxServiceAttribute1();
                break;
            case 'by_service_attribute_2_sum':
                return $this->cart->getSumServiceAttribute2();
                break;
            case 'by_service_attribute_2_max':
                return $this->cart->getMaxServiceAttribute2();
                break;
            case 'by_service_attribute_3_sum':
                return $this->cart->getSumServiceAttribute3();
                break;
            case 'by_service_attribute_3_max':
                return $this->cart->getMaxServiceAttribute3();
                break;
            default:
        }

        return null;
    }

    /**
     * @return float
     */
    protected function getPriceOfPhysicalProducts(): float
    {
        $gross = 0.0;

        if ($this->cart->getProducts()) {
            foreach ($this->cart->getProducts() as $product) {
                if (!$product->getIsVirtualProduct()) {
                    $gross += $product->getGross();
                }
            }
        }

        return $gross;
    }
}
