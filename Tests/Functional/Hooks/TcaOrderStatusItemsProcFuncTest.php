<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Status\PaymentStatus;
use Extcode\Cart\Hooks\OrderStatusItemsProcFunc;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * The status field of the payment with an own status added to the items of
 * the TCA like until cart 12.3, see
 * Tests/Fixtures/cart_order_status_tca/Configuration/TCA/Overrides/.
 *
 * TODO: 13.0.0 Remove with the status in the items of the TCA and cart_order_status_tca.
 */
#[CoversClass(OrderStatusItemsProcFunc::class)]
#[IgnoreDeprecations]
final class TcaOrderStatusItemsProcFuncTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'extcode/cart',
        'typo3conf/ext/cart/Tests/Fixtures/cart_order_status_tca',
    ];

    #[Test]
    public function itemsProcFuncAddsTheStatusOfTheTcaOnceAfterTheRegisteredStatusAndKeepsOtherItems(): void
    {
        $this->expectUserDeprecationMessage(
            'Adding the payment status "refunded_via_tca" to'
            . ' $GLOBALS[\'TCA\'][\'tx_cart_domain_model_order_payment\'][\'columns\'][\'status\'][\'config\'][\'items\'] is deprecated'
            . ' since cart 12.4 and will stop working in cart 13.0.'
            . ' Register the status in Configuration/OrderStatus.php of your extension instead.'
        );

        $tca = $GLOBALS['TCA'];
        self::assertIsArray($tca);
        $config = ArrayUtility::getValueByPath($tca, ['tx_cart_domain_model_order_payment', 'columns', 'status', 'config']);
        self::assertIsArray($config);
        $itemsProcFunc = $config['itemsProcFunc'] ?? null;
        self::assertIsString($itemsProcFunc);
        self::assertNotSame('', $itemsProcFunc);

        $parameters = [
            // the FormEngine passes the items of the TCA and e.g. the ones added by page TSconfig
            'items' => [
                ['label' => 'Refunded via TCA', 'value' => 'refunded_via_tca'],
                ['label' => 'Added by page TSconfig', 'value' => 'page_tsconfig'],
            ],
            'config' => $config,
            'table' => 'tx_cart_domain_model_order_payment',
            'field' => 'status',
        ];

        GeneralUtility::callUserFunction($itemsProcFunc, $parameters, $this);

        $cartLLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:tx_cart_domain_model_order_payment.status.';
        self::assertIsArray($parameters);
        self::assertSame(
            [
                ['label' => $cartLLL . 'open', 'value' => PaymentStatus::OPEN],
                ['label' => $cartLLL . 'pending', 'value' => PaymentStatus::PENDING],
                ['label' => $cartLLL . 'paid', 'value' => PaymentStatus::PAID],
                ['label' => $cartLLL . 'canceled', 'value' => PaymentStatus::CANCELED],
                ['label' => 'Refunded via TCA', 'value' => 'refunded_via_tca'],
                ['label' => 'Added by page TSconfig', 'value' => 'page_tsconfig'],
            ],
            $parameters['items'] ?? null
        );
    }
}
