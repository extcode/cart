<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Dto;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class AvailabilityResponse extends AbstractEntity
{
    protected bool $available = true;

    /**
     * @var FlashMessage[]
     */
    protected array $messages = [];

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    /**
     * @return FlashMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param FlashMessage[] $messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    public function addMessage(FlashMessage $message): void
    {
        $this->messages[] = $message;
    }
}
