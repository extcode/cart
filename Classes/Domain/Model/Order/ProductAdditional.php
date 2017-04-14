<?php

namespace Extcode\Cart\Domain\Model\Order;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Order ProductAdditional Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ProductAdditional extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Additional Type
     *
     * @var string
     * @validate NotEmpty
     */
    protected $additionalType;

    /**
     * Additional Key
     *
     * @var string
     * @validate NotEmpty
     */
    protected $additionalKey;

    /**
     * Additional Value
     *
     * @var string
     * @validate NotEmpty
     */
    protected $additionalValue;

    /**
     * Additional Data
     *
     * @var string
     */
    protected $additionalData = '';

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
}
