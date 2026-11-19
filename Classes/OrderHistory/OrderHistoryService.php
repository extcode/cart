<?php

declare(strict_types=1);

namespace Extcode\Cart\OrderHistory;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;
use Extcode\Cart\OrderStatus\OrderStatusRegistry;
use Extcode\Cart\OrderStatus\ServiceType;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * The changes of an order prepared for the order history in the order
 * details of the backend module.
 */
final readonly class OrderHistoryService
{
    private const LANGUAGE_KEY_PREFIX = 'tx_cart.backend.order.history.';

    public function __construct(
        private OrderChangeRepository $orderChangeRepository,
        private OrderStatusRegistry $orderStatusRegistry,
    ) {
    }

    /**
     * @return list<array{date: DateTimeImmutable, type: string, oldValue: string, newValue: string, user: string}> the latest change first
     */
    public function getEntries(int $orderItemUid): array
    {
        return array_map(
            fn (OrderChange $orderChange): array => [
                'date' => $orderChange->date,
                'type' => self::translate('type.' . $orderChange->type) ?? $orderChange->type,
                'oldValue' => $this->getValueLabel($orderChange->type, $orderChange->oldValue),
                'newValue' => $this->getValueLabel($orderChange->type, $orderChange->newValue),
                'user' => self::getUser($orderChange),
            ],
            $this->orderChangeRepository->findByOrderItem($orderItemUid)
        );
    }

    private function getValueLabel(string $type, string $value): string
    {
        $serviceType = match ($type) {
            OrderChangeType::PAYMENT_STATUS => ServiceType::Payment,
            OrderChangeType::SHIPPING_STATUS => ServiceType::Shipping,
            default => null,
        };
        if ($serviceType === null) {
            return $value;
        }

        return $this->orderStatusRegistry->getStatus($serviceType, $value)?->getLocalizedLabel() ?? $value;
    }

    /**
     * The name of the backend user, otherwise the initiator with the source,
     * e.g. "cart_paypal (API)", otherwise the source, e.g. "Command line".
     */
    private static function getUser(OrderChange $orderChange): string
    {
        if ($orderChange->backendUser > 0) {
            $backendUser = BackendUtility::getRecord('be_users', $orderChange->backendUser, 'username,realName');
            if (is_array($backendUser)) {
                $realName = $backendUser['realName'] ?? '';
                $username = $backendUser['username'] ?? '';

                return is_string($realName) && $realName !== '' ? $realName : (is_string($username) ? $username : '');
            }
        }

        $source = $orderChange->source === null
            ? ''
            : self::translate('source.' . $orderChange->source->value) ?? $orderChange->source->value;

        return match (true) {
            $orderChange->initiator === '' => $source,
            $source === '' => $orderChange->initiator,
            default => $orderChange->initiator . ' (' . $source . ')',
        };
    }

    private static function translate(string $key): ?string
    {
        return LocalizationUtility::translate(self::LANGUAGE_KEY_PREFIX . $key, 'Cart');
    }
}
