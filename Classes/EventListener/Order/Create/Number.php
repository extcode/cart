<?php
declare(strict_types=1);
namespace Extcode\Cart\EventListener\Order\Create;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Repository\Order\ItemRepository as OrderItemRepository;
use Extcode\Cart\Event\Order\NumberGeneratorEventInterface;
use Extcode\Cart\Utility\OrderUtility;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

abstract class Number
{
    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * @var OrderUtility
     */
    protected $orderUtility;

    /**
     * @var array
     */
    protected $options;

    abstract protected function getRegistryName(NumberGeneratorEventInterface $event): string;

    abstract public function __invoke(NumberGeneratorEventInterface $event): void;

    public function __construct(
        PersistenceManager $persistenceManager,
        OrderItemRepository $orderItemRepository,
        OrderUtility $orderUtility,
        array $options = []
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderUtility = $orderUtility;
        $this->options = $options;
    }

    protected function generateNumber(NumberGeneratorEventInterface $event): string
    {
        $registry = GeneralUtility::makeInstance(Registry::class);

        $numberInRegistry = $registry->get('tx_cart', $this->getRegistryName($event));
        $numberInRegistry = $numberInRegistry ? $numberInRegistry + 1 : 1;
        $registry->set('tx_cart', $this->getRegistryName($event), $numberInRegistry);

        $format = $this->options['format'] ?? '%d';
        $numberInRegistryWithOffset = $numberInRegistry + (int)($this->options['offset'] ?? 0);

        return implode([
            $this->options['prefix'] ?? null,
            sprintf($format, $numberInRegistryWithOffset),
            $this->options['suffix'] ?? null,
        ]);
    }
}
