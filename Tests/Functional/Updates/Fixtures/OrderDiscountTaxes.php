<?php

declare(strict_types=1);

use Extcode\Cart\Domain\Model\Cart\CartCouponPercentage;

/*
 * Orders as stored before cart 12.8: the discounts have no taxes per tax class,
 * their tax_class_id keeps its default.
 *
 * Every order contains a shirt (23.80 gross, 3.80 tax at 19 %) and a book
 * (10.70 gross, 0.70 tax at 7 %).
 *
 * 1: percentage coupon (10 %) with shipping and payment at 19 %
 * 2: fixed coupon at 19 %
 * 3: two fixed coupons at 19 %
 * 4: two fixed coupons at 19 % and 7 %, skipped (the split by gross does not match)
 * 5: fixed coupon, skipped (the total price does not add up, e.g. a special service)
 * 6: discount already has taxes per tax class, not touched
 */
return [
    'tx_cart_domain_model_coupon' => [
        ['uid' => 1, 'pid' => 1, 'title' => '10 %', 'code' => 'percentage', 'coupon_type' => CartCouponPercentage::class, 'discount' => 10.0],
        ['uid' => 2, 'pid' => 1, 'title' => '11.90', 'code' => 'fix', 'coupon_type' => 'cartdiscount', 'discount' => 11.90, 'deleted' => 1],
    ],
    'tx_cart_domain_model_order_item' => [
        ['uid' => 1, 'pid' => 1, 'gross' => 34.50, 'net' => 30.00, 'total_gross' => 38.19, 'total_net' => 33.00],
        ['uid' => 2, 'pid' => 1, 'gross' => 34.50, 'net' => 30.00, 'total_gross' => 22.60, 'total_net' => 20.00],
        ['uid' => 3, 'pid' => 1, 'gross' => 34.50, 'net' => 30.00, 'total_gross' => 16.65, 'total_net' => 15.00],
        ['uid' => 4, 'pid' => 1, 'gross' => 34.50, 'net' => 30.00, 'total_gross' => 11.90, 'total_net' => 10.00],
        ['uid' => 5, 'pid' => 1, 'gross' => 34.50, 'net' => 30.00, 'total_gross' => 25.00, 'total_net' => 22.00],
        ['uid' => 6, 'pid' => 1, 'gross' => 34.50, 'net' => 30.00, 'total_gross' => 22.60, 'total_net' => 20.00],
    ],
    'tx_cart_domain_model_order_taxclass' => [
        ['uid' => 1, 'pid' => 1, 'item' => 1, 'title' => 'normal', 'value' => '19', 'calc' => 0.19],
        ['uid' => 2, 'pid' => 1, 'item' => 1, 'title' => 'reduced', 'value' => '7', 'calc' => 0.07],
        ['uid' => 3, 'pid' => 1, 'item' => 2, 'title' => 'normal', 'value' => '19', 'calc' => 0.19],
        ['uid' => 4, 'pid' => 1, 'item' => 2, 'title' => 'reduced', 'value' => '7', 'calc' => 0.07],
        ['uid' => 5, 'pid' => 1, 'item' => 3, 'title' => 'normal', 'value' => '19', 'calc' => 0.19],
        ['uid' => 6, 'pid' => 1, 'item' => 3, 'title' => 'reduced', 'value' => '7', 'calc' => 0.07],
        ['uid' => 7, 'pid' => 1, 'item' => 4, 'title' => 'normal', 'value' => '19', 'calc' => 0.19],
        ['uid' => 8, 'pid' => 1, 'item' => 4, 'title' => 'reduced', 'value' => '7', 'calc' => 0.07],
        ['uid' => 9, 'pid' => 1, 'item' => 5, 'title' => 'normal', 'value' => '19', 'calc' => 0.19],
        ['uid' => 10, 'pid' => 1, 'item' => 5, 'title' => 'reduced', 'value' => '7', 'calc' => 0.07],
        ['uid' => 11, 'pid' => 1, 'item' => 6, 'title' => 'normal', 'value' => '19', 'calc' => 0.19],
        ['uid' => 12, 'pid' => 1, 'item' => 6, 'title' => 'reduced', 'value' => '7', 'calc' => 0.07],
    ],
    'tx_cart_domain_model_order_shipping' => [
        ['uid' => 1, 'pid' => 1, 'item' => 1, 'name' => 'Standard', 'gross' => 5.95, 'net' => 5.00, 'tax' => 0.95, 'tax_class' => 1],
    ],
    'tx_cart_domain_model_order_payment' => [
        ['uid' => 1, 'pid' => 1, 'item' => 1, 'name' => 'Invoice', 'gross' => 1.19, 'net' => 1.00, 'tax' => 0.19, 'tax_class' => 1],
    ],
    'tx_cart_domain_model_order_discount' => [
        ['uid' => 1, 'pid' => 1, 'item' => 1, 'title' => '10 %', 'code' => 'percentage', 'gross' => 3.45, 'net' => 3.00, 'tax' => 0.0],
        ['uid' => 2, 'pid' => 1, 'item' => 2, 'title' => '11.90', 'code' => 'fix', 'gross' => 11.90, 'net' => 10.00, 'tax' => 1.90],
        ['uid' => 3, 'pid' => 1, 'item' => 3, 'title' => '5.95', 'code' => 'first', 'gross' => 5.95, 'net' => 5.00, 'tax' => 0.95],
        ['uid' => 4, 'pid' => 1, 'item' => 3, 'title' => '11.90', 'code' => 'second', 'gross' => 11.90, 'net' => 10.00, 'tax' => 1.90],
        ['uid' => 5, 'pid' => 1, 'item' => 4, 'title' => '11.90', 'code' => 'normal', 'gross' => 11.90, 'net' => 10.00, 'tax' => 1.90],
        ['uid' => 6, 'pid' => 1, 'item' => 4, 'title' => '10.70', 'code' => 'reduced', 'gross' => 10.70, 'net' => 10.00, 'tax' => 0.70],
        ['uid' => 7, 'pid' => 1, 'item' => 5, 'title' => '11.90', 'code' => 'fix', 'gross' => 11.90, 'net' => 10.00, 'tax' => 1.90],
        ['uid' => 8, 'pid' => 1, 'item' => 6, 'title' => '11.90', 'code' => 'fix', 'gross' => 11.90, 'net' => 10.00, 'tax' => 1.90, 'tax_class' => 11, 'taxes' => '{"11":1.9}'],
    ],
    'tx_cart_domain_model_order_tax' => [
        ['uid' => 1, 'pid' => 1, 'item' => 1, 'record_type' => 'tax', 'tax' => 3.80, 'tax_class' => 1],
        ['uid' => 2, 'pid' => 1, 'item' => 1, 'record_type' => 'tax', 'tax' => 0.70, 'tax_class' => 2],
        ['uid' => 3, 'pid' => 1, 'item' => 1, 'record_type' => 'total_tax', 'tax' => 4.56, 'tax_class' => 1],
        ['uid' => 4, 'pid' => 1, 'item' => 1, 'record_type' => 'total_tax', 'tax' => 0.63, 'tax_class' => 2],

        ['uid' => 5, 'pid' => 1, 'item' => 2, 'record_type' => 'tax', 'tax' => 3.80, 'tax_class' => 3],
        ['uid' => 6, 'pid' => 1, 'item' => 2, 'record_type' => 'tax', 'tax' => 0.70, 'tax_class' => 4],
        ['uid' => 7, 'pid' => 1, 'item' => 2, 'record_type' => 'total_tax', 'tax' => 1.90, 'tax_class' => 3],
        ['uid' => 8, 'pid' => 1, 'item' => 2, 'record_type' => 'total_tax', 'tax' => 0.70, 'tax_class' => 4],

        ['uid' => 9, 'pid' => 1, 'item' => 3, 'record_type' => 'tax', 'tax' => 3.80, 'tax_class' => 5],
        ['uid' => 10, 'pid' => 1, 'item' => 3, 'record_type' => 'tax', 'tax' => 0.70, 'tax_class' => 6],
        ['uid' => 11, 'pid' => 1, 'item' => 3, 'record_type' => 'total_tax', 'tax' => 0.95, 'tax_class' => 5],
        ['uid' => 12, 'pid' => 1, 'item' => 3, 'record_type' => 'total_tax', 'tax' => 0.70, 'tax_class' => 6],

        ['uid' => 13, 'pid' => 1, 'item' => 4, 'record_type' => 'tax', 'tax' => 3.80, 'tax_class' => 7],
        ['uid' => 14, 'pid' => 1, 'item' => 4, 'record_type' => 'tax', 'tax' => 0.70, 'tax_class' => 8],
        ['uid' => 15, 'pid' => 1, 'item' => 4, 'record_type' => 'total_tax', 'tax' => 1.90, 'tax_class' => 7],
        ['uid' => 16, 'pid' => 1, 'item' => 4, 'record_type' => 'total_tax', 'tax' => 0.00, 'tax_class' => 8],

        ['uid' => 17, 'pid' => 1, 'item' => 5, 'record_type' => 'tax', 'tax' => 3.80, 'tax_class' => 9],
        ['uid' => 18, 'pid' => 1, 'item' => 5, 'record_type' => 'tax', 'tax' => 0.70, 'tax_class' => 10],
        ['uid' => 19, 'pid' => 1, 'item' => 5, 'record_type' => 'total_tax', 'tax' => 1.90, 'tax_class' => 9],
        ['uid' => 20, 'pid' => 1, 'item' => 5, 'record_type' => 'total_tax', 'tax' => 0.70, 'tax_class' => 10],

        ['uid' => 21, 'pid' => 1, 'item' => 6, 'record_type' => 'tax', 'tax' => 3.80, 'tax_class' => 11],
        ['uid' => 22, 'pid' => 1, 'item' => 6, 'record_type' => 'tax', 'tax' => 0.70, 'tax_class' => 12],
        ['uid' => 23, 'pid' => 1, 'item' => 6, 'record_type' => 'total_tax', 'tax' => 1.90, 'tax_class' => 11],
        ['uid' => 24, 'pid' => 1, 'item' => 6, 'record_type' => 'total_tax', 'tax' => 0.70, 'tax_class' => 12],
    ],
];
