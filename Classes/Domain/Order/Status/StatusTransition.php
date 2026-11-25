<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Status;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Closure;
use DateTimeImmutable;
use Extcode\Cart\Domain\Order\History\OrderChange;
use Extcode\Cart\Domain\Order\History\OrderChangeRepository;
use Extcode\Cart\Domain\Order\History\OrderChangeSource;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * @internal
 */
final readonly class StatusTransition
{
    public function __construct(
        private ConnectionPool $connectionPool,
        private OrderChangeRepository $orderChangeRepository,
        private Context $context,
    ) {
    }

    public function resolveSource(?ServerRequestInterface $request, ?OrderChangeSource $source): OrderChangeSource
    {
        if ($source !== null) {
            return $source;
        }

        $request ??= $GLOBALS['TYPO3_REQUEST'] ?? null;

        return OrderChangeSource::fromRequest($request instanceof ServerRequestInterface ? $request : null);
    }

    public function getNow(): DateTimeImmutable
    {
        $now = $this->context->getPropertyFromAspect('date', 'full');

        return $now instanceof DateTimeImmutable ? $now : new DateTimeImmutable();
    }

    public function getBackendUserUid(): int
    {
        $uid = $this->context->getPropertyFromAspect('backend.user', 'id', 0);

        return is_int($uid) ? $uid : 0;
    }

    /**
     * @param Closure(string $fromStatus, string $toStatus, DateTimeImmutable $date): bool $changeStoredStatusIfExpected
     * @param Closure(): ?string $findStoredStatus
     */
    public function apply(
        OrderChange $orderChange,
        Closure $changeStoredStatusIfExpected,
        Closure $findStoredStatus,
    ): StatusChangeResult {
        $connection = $this->connectionPool->getConnectionForTable(OrderChangeRepository::TABLE_NAME);

        return $connection->transactional(
            function () use ($orderChange, $changeStoredStatusIfExpected, $findStoredStatus): StatusChangeResult {
                $isChanged = $changeStoredStatusIfExpected(
                    $orderChange->oldValue,
                    $orderChange->newValue,
                    $orderChange->date
                );

                if (!$isChanged) {
                    $storedStatus = $findStoredStatus();

                    return $storedStatus === $orderChange->newValue
                        ? new StatusChangeResult(StatusChangeOutcome::Unchanged)
                        : new StatusChangeResult(StatusChangeOutcome::Stale, storedStatus: $storedStatus);
                }

                return new StatusChangeResult(
                    StatusChangeOutcome::Changed,
                    $orderChange->withUid($this->orderChangeRepository->add($orderChange))
                );
            }
        );
    }
}
