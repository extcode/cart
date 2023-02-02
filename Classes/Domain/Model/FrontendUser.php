<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class FrontendUser extends AbstractEntity
{
    /**
     * @var ObjectStorage<FrontendUserGroup>
     */
    protected ObjectStorage $usergroup;

    public function construct()
    {
        $this->usergroup = new ObjectStorage();
    }

    /**
     * @return ObjectStorage<FrontendUserGroup>
     */
    public function getUsergroup(): ObjectStorage
    {
        return $this->usergroup;
    }
}
