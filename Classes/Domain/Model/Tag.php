<?php

namespace Extcode\Cart\Domain\Model;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
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
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
