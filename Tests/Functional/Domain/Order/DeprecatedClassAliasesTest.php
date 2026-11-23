<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Domain\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\History\OrderChangeRepository;
use Extcode\Cart\Domain\Order\History\OrderHistoryService;
use Extcode\Cart\Domain\Order\Status\Mail\StatusChangeMailRuleLoader;
use Extcode\Cart\Domain\Order\Status\OrderStatusConfigurationBuilder;
use Extcode\Cart\Domain\Order\Status\OrderStatusRegistry;
use Extcode\Cart\Domain\Order\Status\StatusChangeService;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @deprecated remove together with Migrations/Code/ClassAliasMap.php in cart 13.0
 */
#[CoversNothing]
final class DeprecatedClassAliasesTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    #[DataProvider('classAliasDataProvider')]
    #[Test]
    public function oldClassNameIsAnAliasOfTheNewClass(string $oldClassName, string $newClassName): void
    {
        self::assertTrue(class_exists($oldClassName) || enum_exists($oldClassName));
        self::assertSame($newClassName, (new ReflectionClass($oldClassName))->getName());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function classAliasDataProvider(): iterable
    {
        $classAliasMap = require __DIR__ . '/../../../../Migrations/Code/ClassAliasMap.php';
        self::assertIsArray($classAliasMap);
        foreach ($classAliasMap as $oldClassName => $newClassName) {
            self::assertIsString($oldClassName);
            self::assertIsString($newClassName);
            yield $oldClassName => [$oldClassName, $newClassName];
        }
    }

    /**
     * @param class-string $oldServiceId
     * @param class-string $newClassName
     */
    #[DataProvider('serviceAliasDataProvider')]
    #[IgnoreDeprecations]
    #[Test]
    public function oldServiceIdReturnsTheNewService(string $oldServiceId, string $newClassName): void
    {
        $container = $this->getContainer();

        self::assertTrue($container->has($oldServiceId));
        self::assertInstanceOf($newClassName, $container->get($oldServiceId));
        self::assertInstanceOf($newClassName, GeneralUtility::makeInstance($oldServiceId));
    }

    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function serviceAliasDataProvider(): iterable
    {
        yield 'OrderChangeRepository' => ['Extcode\\Cart\\OrderHistory\\OrderChangeRepository', OrderChangeRepository::class];
        yield 'OrderHistoryService' => ['Extcode\\Cart\\OrderHistory\\OrderHistoryService', OrderHistoryService::class];
        yield 'StatusChangeMailRuleLoader' => ['Extcode\\Cart\\OrderStatus\\Mail\\StatusChangeMailRuleLoader', StatusChangeMailRuleLoader::class];
        yield 'OrderStatusConfigurationBuilder' => ['Extcode\\Cart\\OrderStatus\\OrderStatusConfigurationBuilder', OrderStatusConfigurationBuilder::class];
        yield 'OrderStatusRegistry' => ['Extcode\\Cart\\OrderStatus\\OrderStatusRegistry', OrderStatusRegistry::class];
        yield 'StatusChangeService' => ['Extcode\\Cart\\OrderStatus\\StatusChangeService', StatusChangeService::class];
    }
}
