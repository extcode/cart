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
     * Additional Type
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $additionalType;

    /**
     * Additional Key
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $additionalKey;

    /**
     * Additional Value
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $additionalValue;

    /**
     * Additional Data
     *
     * @var string
     */
    protected $additionalData = '';

    /**
     * Additional
     *
     * @var string
     */
    protected $additional;

    /**
     * __construct
     *
     * @param string $additionalType
     * @param string $additionalKey
     * @param string $additionalValue
     * @param string $additionalData
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $additionalType,
        $additionalKey,
        $additionalValue,
        $additionalData = ''
    ) {
        if (!$additionalType) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $additionalType for constructor.',
                1456828210
            );
        }
        if (!$additionalKey) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $additionalKey for constructor.',
                1456828220
            );
        }
        if (!$additionalValue) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $additionalValue for constructor.',
                1456828230
            );
        }

        $this->additionalType = $additionalType;
        $this->additionalKey = $additionalKey;
        $this->additionalValue = $additionalValue;
        $this->additionalData = $additionalData;
    }

    /**
     * Returns Additional Type
     *
     * @return string
     */
    public function getAdditionalType()
    {
        return $this->additionalType;
    }

    /**
     * Returns Additional Key
     *
     * @return string
     */
    public function getAdditionalKey()
    {
        return $this->additionalKey;
    }

    /**
     * Returns Additional Value
     *
     * @return string
     */
    public function getAdditionalValue()
    {
        return $this->additionalValue;
    }

    /**
     * Returns Additional Data
     *
     * @return string
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     * Sets Additional Data
     *
     * @param string $additionalData
     */
    public function setAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;
    }

    /**
     * @return array
     */
    public function getAdditional()
    {
        return json_decode($this->additional, 1);
    }

    /**
     * @return string
     */
    public function getAdditionalJson()
    {
        return $this->additional;
    }

    /**
     * @param array $additional
     */
    public function setAdditional($additional)
    {
        $this->additional = json_encode($additional);
    }
}
