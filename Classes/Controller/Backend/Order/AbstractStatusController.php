<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Controller\Backend\ActionController;
use Extcode\Cart\Domain\Log\LogServiceInterface;
use Extcode\Cart\Domain\Log\Model\Log;
use Extcode\Cart\Domain\Model\Order\AbstractService;
use Extcode\Cart\Event\Order\AfterStatusChangeEvent;
use Extcode\Cart\Event\Order\UpdateServiceEvent;
use Extcode\Cart\OrderStatus\StatusChangeOutcome;
use Extcode\Cart\OrderStatus\StatusChangeService;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Changes the status of the payment or the shipping of an order in the order
 * details of the backend module.
 */
abstract class AbstractStatusController extends ActionController
{
    public function __construct(
        private readonly StatusChangeService $statusChangeService,
        private readonly LogServiceInterface $logService,
        private readonly ListenerProvider $listenerProvider,
    ) {
    }

    /**
     * @param 'payment'|'shipping' $serviceName
     */
    protected function changeStatus(AbstractService $service, string $status, string $fromStatus, string $serviceName): ResponseInterface
    {
        $orderItem = $service->getItem();
        $orderItemUid = $orderItem?->getUid();
        if ($orderItem === null || $orderItemUid === null) {
            throw new InvalidArgumentException('Method should only called for persisted orders.', 1774715307);
        }

        $result = $this->statusChangeService->change($service, $status, $fromStatus, request: $this->request);

        if ($result->isChanged()) {
            $this->logService->write(
                Log::info(
                    $orderItemUid,
                    'update' . ucfirst($serviceName),
                    ucfirst($serviceName) . ' was set to ' . $status . '.',
                    [
                        'time' => time(),
                    ]
                )
            );

            $this->dispatchUpdateServiceEvent($service);
        }

        $labelPrefix = 'tx_cart.controller.order.action.update_' . $serviceName . '_action.';
        match ($result->outcome) {
            StatusChangeOutcome::Changed => $this->addFlashMessage(
                self::translate($labelPrefix . 'success')
            ),
            StatusChangeOutcome::Unchanged => $this->addFlashMessage(
                self::translate($labelPrefix . 'unchanged'),
                '',
                ContextualFeedbackSeverity::INFO
            ),
            StatusChangeOutcome::Stale => $this->addFlashMessage(
                self::translate($labelPrefix . 'stale'),
                '',
                ContextualFeedbackSeverity::WARNING
            ),
            StatusChangeOutcome::UnknownStatus => $this->addFlashMessage(
                self::translate($labelPrefix . 'unknown_status', [$status]),
                '',
                ContextualFeedbackSeverity::ERROR
            ),
            StatusChangeOutcome::Vetoed => $this->addFlashMessage(
                self::translate($labelPrefix . 'vetoed', [$result->vetoReason]),
                '',
                ContextualFeedbackSeverity::WARNING
            ),
        };

        return $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $orderItem]);
    }

    /**
     * @deprecated since cart 12.4, will be removed in cart 13.0
     *
     * TODO: 13.0.0 Remove together with the UpdateServiceEvent, see Deprecation #840.
     */
    private function dispatchUpdateServiceEvent(AbstractService $service): void
    {
        $event = new UpdateServiceEvent($service);

        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            trigger_error(
                'The event ' . UpdateServiceEvent::class . ' is deprecated since cart 12.4 and will be removed in cart 13.0.'
                . ' Listen to the ' . AfterStatusChangeEvent::class . ' instead.',
                E_USER_DEPRECATED
            );
            break;
        }

        $this->eventDispatcher->dispatch($event);
    }

    /**
     * @param list<string> $arguments
     */
    private static function translate(string $key, array $arguments = []): string
    {
        return LocalizationUtility::translate($key, 'Cart', $arguments) ?? $key;
    }
}
