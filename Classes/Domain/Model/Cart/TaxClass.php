<?php
declare(strict_types = 1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class TaxClass
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var float
     */
    protected $calc;

    /**
     * @var string
     */
    protected $title;

    /**
     * @param int $id
     * @param string $value
     * @param float $calc
     * @param string $title
     */
    public function __construct(
        int $id,
        string $value,
        float $calc,
        string $title
    ) {
        $this->id = $id;
        $this->value = $value;
        $this->calc = $calc;
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
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
}
