<?php

namespace Extcode\Cart\Domain\Model\Product;

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
 * Product Tag Model
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Tag extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
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
     * @param string $title
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $title
    ) {
        if (!$title) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $title for constructor.',
                1460206410
            );
        }

        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
