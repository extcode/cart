<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\View\ModifyViewEvent;

abstract class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    protected function dispatchModifyViewEvent(): void
    {
        $modifyViewEvent = new ModifyViewEvent(
            $this->request,
            $this->settings,
            $this->view
        );

        $this->eventDispatcher->dispatch($modifyViewEvent);
    }
}
