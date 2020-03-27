<?php

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
     * Id
     *
     * @var int
     * @validate NotEmpty
     */
    protected $id;

    /**
     * Value
     *
     * @var string
     * @validate NotEmpty
     */
    protected $value;

    /**
     * Calc
     *
     * @var float
     * @validate NotEmpty
     */
    protected $calc;

    /**
     * Title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title;

    /**
     * __construct
     *
     * @param int $id
     * @param string $value
     * @param float $calc
     * @param string $title
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($id, $value, $calc, $title)
    {
        if (!$id) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $id for constructor.',
                1413981328
            );
        }
        if (empty($value) && ($value !== '0')) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $value for constructor.',
                1413981329
            );
        }
        if (($calc === null) || ($calc < 0.0)) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $calc for constructor.',
                1413981330
            );
        }
        if (empty($title) && ($title !== '0')) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $title for constructor.',
                1413981331
            );
        }

        $this->id = $id;
        $this->value = $value;
        $this->calc = $calc;
        $this->title = $title;
    }

    /**
     * Gets Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets Value
     *
     * @return mixed|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Gets Calc
     *
     * @return int
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
}
