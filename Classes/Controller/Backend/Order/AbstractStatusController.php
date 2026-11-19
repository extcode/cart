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
use Extcode\Cart\Event\Order\UpdateServiceEvent;
use Extcode\Cart\Service\Order\StatusChangeResult;
use Extcode\Cart\Service\Order\StatusChangeService;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Changes the status of the payment or the shipping of an order in the order
 * details of the backend module.
 */
abstract class AbstractStatusController extends ActionController
{
    public function __construct(
        protected readonly StatusChangeService $statusChangeService,
        protected readonly LogServiceInterface $logService,
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

        if ($result === StatusChangeResult::Changed) {
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

            $this->eventDispatcher->dispatch(new UpdateServiceEvent($service));
        }

        $labelPrefix = 'tx_cart.controller.order.action.update_' . $serviceName . '_action.';
        match ($result) {
            StatusChangeResult::Changed => $this->addFlashMessage(
                self::translate($labelPrefix . 'success')
            ),
            StatusChangeResult::Unchanged => $this->addFlashMessage(
                self::translate($labelPrefix . 'unchanged'),
                '',
                ContextualFeedbackSeverity::INFO
            ),
            StatusChangeResult::Stale => $this->addFlashMessage(
                self::translate($labelPrefix . 'stale'),
                '',
                ContextualFeedbackSeverity::WARNING
            ),
            StatusChangeResult::UnknownStatus => $this->addFlashMessage(
                self::translate($labelPrefix . 'unknown_status', [$status]),
                '',
                ContextualFeedbackSeverity::ERROR
            ),
        };

        return $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $orderItem]);
    }

    /**
     * @param list<string> $arguments
     */
    private static function translate(string $key, array $arguments = []): string
    {
        return LocalizationUtility::translate($key, 'Cart', $arguments) ?? $key;
    }
}
