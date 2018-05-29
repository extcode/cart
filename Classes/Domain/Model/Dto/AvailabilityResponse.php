<?php

namespace Extcode\Cart\Domain\Model\Dto;

/**
 * This file is part of the "cart_products" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class AvailabilityResponse extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var bool
     */
    protected $available = true;

    /**
     * @var \TYPO3\CMS\Core\Messaging\FlashMessage[]
     */
    protected $messages = [];

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->available;
    }

    /**
     * @param bool $available
     */
    public function setAvailable(bool $available)
    {
        $this->available = $available;
    }

    /**
     * @return \TYPO3\CMS\Core\Messaging\FlashMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param \TYPO3\CMS\Core\Messaging\FlashMessage[] $messages
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @param \TYPO3\CMS\Core\Messaging\FlashMessage $message
     */
    public function addMessage(\TYPO3\CMS\Core\Messaging\FlashMessage $message)
    {
        array_push($this->messages, $message);
    }
}
