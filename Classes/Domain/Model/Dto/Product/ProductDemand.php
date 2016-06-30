<?php

namespace Extcode\Cart\Domain\Model\Dto\Product;

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
 * Data Transfer Object Product
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ProductDemand extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Sku
     *
     * @var string
     */
    protected $sku;

    /**
     * Title
     *
     * @var string
     */
    protected $title;

    /**
     * Order
     *
     * @var string
     */
    protected $order;

    /**
     * Action
     *
     * @var string
     */
    protected $action;

    /**
     * Class
     *
     * @var string
     */
    protected $class;

    /**
     * Returns sku
     *
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Sets sku
     *
     * @param mixed $sku
     *
     * @return void
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * Returns title
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets title
     *
     * @param mixed $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns order
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Sets order
     *
     * @param string $order
     *
     * @return void
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Returns action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Sets action
     *
     * @param string $action
     *
     * @return void
     */
    public function setAction($action)
    {
        $this->action = $action;
    }
    /**
     * Returns class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Sets class
     *
     * @param string $class
     *
     * @return void
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Sets action and class
     *
     * @param string $action
     * @param string $controller
     *
     * @return void
     */
    public function setActionAndClass($action, $controller)
    {
        $this->action = $action;
        $this->class = $controller;
    }
}
