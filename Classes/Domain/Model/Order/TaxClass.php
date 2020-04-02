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
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $title = '';

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $value = '';

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $calc = 0.0;

    /**
     * @param string $title
     * @param string $value
     * @param float $calc
     */
    public function __construct(
        string $title,
        string $value,
        float $calc
    ) {
        $this->title = $title;
        $this->value = $value;
        $this->calc = $calc;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'value' => $this->getValue(),
            'calc' => $this->getCalc(),
        ];
    }

    /**
     * @return float
     */
    public function getCalc(): float
    {
        return $this->calc;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
