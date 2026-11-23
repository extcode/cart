<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\Number\NumberCounter;
use Extcode\Cart\Domain\Repository\Order\ItemRepository as OrderItemRepository;
use Extcode\Cart\Event\Order\NumberGeneratorEventInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

abstract class Number
{
    private readonly NumberCounter $numberCounter;

    public function __construct(
        protected PersistenceManager $persistenceManager,
        protected OrderItemRepository $orderItemRepository,
        protected array $options = [],
        ?NumberCounter $numberCounter = null
    ) {
        $this->numberCounter = $numberCounter ?? GeneralUtility::makeInstance(NumberCounter::class);
    }

    abstract public function __invoke(NumberGeneratorEventInterface $event): void;

    abstract protected function getRegistryName(NumberGeneratorEventInterface $event): string;

    protected function generateNumber(NumberGeneratorEventInterface $event): string
    {
        $numberInRegistry = $this->numberCounter->increment($this->getRegistryName($event));

        $format = $this->options['format'] ?? '%d';
        $numberInRegistryWithOffset = $numberInRegistry + (int) ($this->options['offset'] ?? 0);
        $number = sprintf($format, $numberInRegistryWithOffset);

        return implode('', [
            is_string($this->options['prefix'] ?? null) ? $this->options['prefix'] : '',
            $number,
            is_string($this->options['suffix'] ?? null) ? $this->options['suffix'] : '',
        ]);
    }
}
