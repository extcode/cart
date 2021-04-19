<?php

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class ProductAdditional extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $additionalType;

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $additionalKey;

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $additionalValue;

    /**
     * @var string
     */
    protected $additionalData = '';

    /**
     * @var string
     */
    protected $additional;

    /**
     * @param string $type
     * @param string $key
     * @param string $value
     * @param string $data
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $type,
        string $key,
        string $value,
        string $data = ''
    ) {
        $this->additionalType = $type;
        $this->additionalKey = $key;
        $this->additionalValue = $value;
        $this->additionalData = $data;
    }

    /**
     * @return string
     */
    public function getAdditionalType(): string
    {
        return $this->additionalType;
    }

    /**
     * @return string
     */
    public function getAdditionalKey(): string
    {
        return $this->additionalKey;
    }

    /**
     * @return string
     */
    public function getAdditionalValue(): string
    {
        return $this->additionalValue;
    }

    /**
     * @return string
     */
    public function getAdditionalData(): string
    {
        return $this->additionalData;
    }

    /**
     * @param string $additionalData
     */
    public function setAdditionalData(string $additionalData)
    {
        $this->additionalData = $additionalData;
    }

    /**
     * @return array
     */
    public function getAdditional(): array
    {
        if ($this->additional) {
            return json_decode($this->additional, true);
        }

        return [];
    }

    /**
     * @param array $additional
     */
    public function setAdditional(array $additional)
    {
        $this->additional = json_encode($additional);
    }
}
