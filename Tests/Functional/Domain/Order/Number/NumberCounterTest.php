<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Domain\Order\Number;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\Number\NumberCounter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(NumberCounter::class)]
final class NumberCounterTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    private NumberCounter $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new NumberCounter($this->getConnectionPool());
    }

    #[Test]
    public function newCounterStartsWithOne(): void
    {
        self::assertSame(1, $this->subject->increment('lastInvoice_1'));
        self::assertSame(2, $this->subject->increment('lastInvoice_1'));
        self::assertSame(3, $this->subject->increment('lastInvoice_1'));
    }

    #[Test]
    public function countersAreIndependentOfEachOther(): void
    {
        self::assertSame(1, $this->subject->increment('lastInvoice_1'));
        self::assertSame(1, $this->subject->increment('lastInvoice_2'));
        self::assertSame(1, $this->subject->increment('lastOrder_1'));
        self::assertSame(2, $this->subject->increment('lastInvoice_1'));
    }

    #[Test]
    public function continuesCounterWrittenByTheRegistry(): void
    {
        GeneralUtility::makeInstance(Registry::class)->set('tx_cart', 'lastInvoice_1', 41);

        self::assertSame(42, $this->subject->increment('lastInvoice_1'));
    }

    #[Test]
    public function storesCounterReadableByTheRegistry(): void
    {
        $this->subject->increment('lastInvoice_1');
        $this->subject->increment('lastInvoice_1');

        self::assertSame(2, $this->getStoredValue('tx_cart', 'lastInvoice_1'));
    }

    #[Test]
    public function readsCurrentValueEvenIfTheRegistryCachedAnOlderOne(): void
    {
        $registry = GeneralUtility::makeInstance(Registry::class);
        $registry->set('tx_cart', 'lastInvoice_1', 5);

        // another request increments the counter in the meantime
        $this->getConnectionPool()
            ->getConnectionForTable('sys_registry')
            ->update(
                'sys_registry',
                ['entry_value' => serialize(7)],
                ['entry_namespace' => 'tx_cart', 'entry_key' => 'lastInvoice_1']
            )
        ;
        self::assertSame(5, $registry->get('tx_cart', 'lastInvoice_1'));

        self::assertSame(8, $this->subject->increment('lastInvoice_1'));
    }

    #[Test]
    public function doesNotChangeEntriesOfOtherNamespaces(): void
    {
        GeneralUtility::makeInstance(Registry::class)->set('tx_other', 'lastInvoice_1', 100);

        self::assertSame(1, $this->subject->increment('lastInvoice_1'));
        self::assertSame(100, $this->getStoredValue('tx_other', 'lastInvoice_1'));
    }

    private function getStoredValue(string $namespace, string $key): mixed
    {
        $storedValue = $this->getConnectionPool()
            ->getConnectionForTable('sys_registry')
            ->select(['entry_value'], 'sys_registry', ['entry_namespace' => $namespace, 'entry_key' => $key])
            ->fetchOne()
        ;
        self::assertIsString($storedValue);

        return unserialize($storedValue, ['allowed_classes' => false]);
    }
}
