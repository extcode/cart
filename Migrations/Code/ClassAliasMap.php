<?php

declare(strict_types=1);
use Extcode\Cart\Domain\Order\History\OrderChange;
use Extcode\Cart\Domain\Order\History\OrderChangeRepository;
use Extcode\Cart\Domain\Order\History\OrderChangeSource;
use Extcode\Cart\Domain\Order\History\OrderChangeType;
use Extcode\Cart\Domain\Order\History\OrderHistoryService;
use Extcode\Cart\Domain\Order\Status\Mail\InvalidStatusChangeMailRule;
use Extcode\Cart\Domain\Order\Status\Mail\StatusChangeMailRule;
use Extcode\Cart\Domain\Order\Status\Mail\StatusChangeMailRuleLoader;
use Extcode\Cart\Domain\Order\Status\OrderStatusConfigurationBuilder;
use Extcode\Cart\Domain\Order\Status\OrderStatusRegistry;
use Extcode\Cart\Domain\Order\Status\PaymentStatus;
use Extcode\Cart\Domain\Order\Status\ServiceType;
use Extcode\Cart\Domain\Order\Status\ShippingStatus;
use Extcode\Cart\Domain\Order\Status\Status;
use Extcode\Cart\Domain\Order\Status\StatusChangeOutcome;
use Extcode\Cart\Domain\Order\Status\StatusChangeResult;
use Extcode\Cart\Domain\Order\Status\StatusChangeService;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

// @deprecated since cart 12.7, will be removed in cart 13.0
return [
    'Extcode\\Cart\\OrderHistory\\OrderChange' => OrderChange::class,
    'Extcode\\Cart\\OrderHistory\\OrderChangeRepository' => OrderChangeRepository::class,
    'Extcode\\Cart\\OrderHistory\\OrderChangeSource' => OrderChangeSource::class,
    'Extcode\\Cart\\OrderHistory\\OrderChangeType' => OrderChangeType::class,
    'Extcode\\Cart\\OrderHistory\\OrderHistoryService' => OrderHistoryService::class,
    'Extcode\\Cart\\OrderStatus\\Mail\\InvalidStatusChangeMailRule' => InvalidStatusChangeMailRule::class,
    'Extcode\\Cart\\OrderStatus\\Mail\\StatusChangeMailRuleLoader' => StatusChangeMailRuleLoader::class,
    'Extcode\\Cart\\OrderStatus\\Mail\\StatusChangeMailRule' => StatusChangeMailRule::class,
    'Extcode\\Cart\\OrderStatus\\OrderStatusConfigurationBuilder' => OrderStatusConfigurationBuilder::class,
    'Extcode\\Cart\\OrderStatus\\OrderStatusRegistry' => OrderStatusRegistry::class,
    'Extcode\\Cart\\OrderStatus\\PaymentStatus' => PaymentStatus::class,
    'Extcode\\Cart\\OrderStatus\\ServiceType' => ServiceType::class,
    'Extcode\\Cart\\OrderStatus\\ShippingStatus' => ShippingStatus::class,
    'Extcode\\Cart\\OrderStatus\\StatusChangeOutcome' => StatusChangeOutcome::class,
    'Extcode\\Cart\\OrderStatus\\StatusChangeResult' => StatusChangeResult::class,
    'Extcode\\Cart\\OrderStatus\\StatusChangeService' => StatusChangeService::class,
    'Extcode\\Cart\\OrderStatus\\Status' => Status::class,
];
