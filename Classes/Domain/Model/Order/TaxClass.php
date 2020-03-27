<?php

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class TaxClass extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * Title
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $title;

    /**
     * Value
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $value;

    /**
     * Calc
     *
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $calc = 0.0;

    /**
     * __construct
     *
     * @param string $title
     * @param string $value
     * @param float $calc
     */
    public function __construct(
        $title,
        $value,
        $calc
    ) {
        if (!$title) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $title for constructor.',
                1456830910
            );
        }
        if (empty($value) && $value !== '0') {
            throw new \InvalidArgumentException(
                'You have to specify a valid $value for constructor.',
                1456830920
            );
        }
        if ($calc === null) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $calc for constructor.',
                1456830930
            );
        }

        $this->title = $title;
        $this->value = $value;
        $this->calc = $calc;
    }

    /**
     * Returns TaxClassArray
     *
     * @return array
     */
    public function toArray()
    {
        $taxClassArray = [
            'title' => $this->getTitle(),
            'value' => $this->getValue(),
            'calc' => $this->getCalc(),
        ];

        return $taxClassArray;
    }

    /**
     * Gets Calc
     *
     * @return float
     */
    public function getCalc()
    {
        return $this->calc;
    }

    /**
     * Gets Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets Value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
