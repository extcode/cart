<?php

declare(strict_types=1);

use Extcode\Cart\Domain\Model\Order\BillingAddress;

/*
 * Orders within the period of the dashboard widgets (last 28 days).
 * The dates are relative to the start of the test run, so the charts always
 * show these orders.
 */
$orders = [
    // uid => [order date, payment status, shipping status, net, gross]
    1 => ['-20 days 10:00', 'paid', 'shipped', '41.93', '49.90'],
    2 => ['-10 days 11:00', 'paid', 'open', '100.00', '119.00'],
    3 => ['-3 days 12:00', 'open', 'open', '21.43', '25.50'],
    4 => ['-1 day 13:00', 'paid', 'open', '75.59', '89.95'],
    5 => ['today 00:01', 'paid', 'shipped', '12.61', '15.00'],
];

$dataSet = [
    'tx_cart_domain_model_order_item' => [],
    'tx_cart_domain_model_order_address' => [],
    'tx_cart_domain_model_order_payment' => [],
    'tx_cart_domain_model_order_shipping' => [],
];

foreach ($orders as $uid => [$orderDate, $paymentStatus, $shippingStatus, $totalNet, $totalGross]) {
    $timestamp = (int) strtotime($orderDate);

    $dataSet['tx_cart_domain_model_order_item'][] = [
        'uid' => $uid,
        'pid' => 105,
        'crdate' => $timestamp,
        'billing_address' => 1,
        'payment' => 1,
        'shipping' => 1,
        'order_number' => 'O-' . (1000 + $uid),
        'order_date' => $timestamp,
        'currency' => '€',
        'currency_code' => 'EUR',
        'currency_sign' => '€',
        'currency_translation' => '1',
        'total_net' => $totalNet,
        'total_gross' => $totalGross,
    ];
    $dataSet['tx_cart_domain_model_order_address'][] = [
        'uid' => $uid,
        'pid' => 105,
        'item' => $uid,
        'record_type' => '\\' . BillingAddress::class,
        'salutation' => 'Mrs',
        'first_name' => 'Customer',
        'last_name' => (string) $uid,
        'email' => 'customer-' . $uid . '@mail.example',
    ];
    $dataSet['tx_cart_domain_model_order_payment'][] = [
        'uid' => $uid,
        'pid' => 105,
        'item' => $uid,
        'name' => 'Payment ' . $uid,
        'status' => $paymentStatus,
    ];
    $dataSet['tx_cart_domain_model_order_shipping'][] = [
        'uid' => $uid,
        'pid' => 105,
        'item' => $uid,
        'name' => 'Shipping ' . $uid,
        'status' => $shippingStatus,
    ];
}

return $dataSet;
