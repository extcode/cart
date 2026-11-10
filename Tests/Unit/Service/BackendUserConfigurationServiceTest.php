<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Service\BackendUserConfigurationService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use stdClass;

#[CoversClass(BackendUserConfigurationService::class)]
class BackendUserConfigurationServiceTest extends UnitTestCase
{
    private const ORDER_SEARCH_ARGUMENTS = [
        'customer' => 'Smith',
        'orderNumber' => 'O-202605%',
        'orderDateStart' => '2026-05-01T00:00:00+00:00',
        'orderDateEnd' => '2026-05-31T00:00:00+00:00',
        'invoiceNumber' => 'I-202605%',
        'invoiceDateStart' => '2026-06-01T00:00:00+00:00',
        'invoiceDateEnd' => '2026-06-30T00:00:00+00:00',
        'paymentStatus' => 'paid',
        'shippingStatus' => 'all',
    ];

    private BackendUserAuthentication&MockObject $backendUser;

    private BackendUserConfigurationService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backendUser = $this->createMock(BackendUserAuthentication::class);
        $GLOBALS['BE_USER'] = $this->backendUser;

        $this->subject = new BackendUserConfigurationService();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['BE_USER']);

        parent::tearDown();
    }

    #[Test]
    public function saveOrderSearchArgumentsWritesAllKnownSearchArgumentsToTheUserConfiguration(): void
    {
        $this->backendUser->expects(self::once())->method('writeUC');

        $this->subject->saveOrderSearchArguments(self::ORDER_SEARCH_ARGUMENTS);

        self::assertSame(
            self::ORDER_SEARCH_ARGUMENTS,
            $this->backendUser->uc[BackendUserConfigurationService::USER_CONFIGURATION_KEY_ORDER_SEARCH]
        );
    }

    #[Test]
    public function saveOrderSearchArgumentsWritesOnlyKnownSearchArgumentsWithScalarValues(): void
    {
        $this->backendUser->expects(self::once())->method('writeUC');

        $this->subject->saveOrderSearchArguments([
            'customer' => ['Smith'],
            'orderNumber' => 'O-202605%',
            'invoiceNumber' => null,
            'paymentStatus' => 2,
            'unknownArgument' => 'value',
            0 => 'value',
        ]);

        self::assertSame(
            [
                'orderNumber' => 'O-202605%',
                'paymentStatus' => '2',
            ],
            $this->backendUser->uc[BackendUserConfigurationService::USER_CONFIGURATION_KEY_ORDER_SEARCH]
        );
    }

    #[Test]
    public function saveOrderSearchArgumentsKeepsOtherEntriesOfTheUserConfiguration(): void
    {
        $this->backendUser->uc = ['lang' => 'de'];

        $this->subject->saveOrderSearchArguments(['customer' => 'Smith']);

        self::assertSame(
            [
                'lang' => 'de',
                BackendUserConfigurationService::USER_CONFIGURATION_KEY_ORDER_SEARCH => ['customer' => 'Smith'],
            ],
            $this->backendUser->uc
        );
    }

    #[Test]
    public function getOrderSearchArgumentsReturnsTheSearchArgumentsOfTheUserConfiguration(): void
    {
        $this->backendUser->uc[BackendUserConfigurationService::USER_CONFIGURATION_KEY_ORDER_SEARCH] = self::ORDER_SEARCH_ARGUMENTS;

        self::assertSame(
            self::ORDER_SEARCH_ARGUMENTS,
            $this->subject->getOrderSearchArguments()
        );
    }

    #[Test]
    public function getOrderSearchArgumentsReturnsOnlyKnownSearchArgumentsWithScalarValues(): void
    {
        $this->backendUser->uc[BackendUserConfigurationService::USER_CONFIGURATION_KEY_ORDER_SEARCH] = [
            'customer' => ['Smith'],
            'orderNumber' => 'O-202605%',
            'unknownArgument' => 'value',
        ];

        self::assertSame(
            ['orderNumber' => 'O-202605%'],
            $this->subject->getOrderSearchArguments()
        );
    }

    #[Test]
    public function getOrderSearchArgumentsReturnsAnEmptyArrayIfNoSearchArgumentsHaveBeenSaved(): void
    {
        self::assertSame(
            [],
            $this->subject->getOrderSearchArguments()
        );
    }

    #[DataProvider('invalidUserConfigurationDataProvider')]
    #[Test]
    public function getOrderSearchArgumentsReturnsAnEmptyArrayForAnInvalidUserConfiguration(mixed $userConfiguration): void
    {
        $this->backendUser->uc[BackendUserConfigurationService::USER_CONFIGURATION_KEY_ORDER_SEARCH] = $userConfiguration;

        self::assertSame(
            [],
            $this->subject->getOrderSearchArguments()
        );
    }

    /**
     * @return iterable<string, array{userConfiguration: mixed}>
     */
    public static function invalidUserConfigurationDataProvider(): iterable
    {
        yield 'null' => ['userConfiguration' => null];
        yield 'string' => ['userConfiguration' => 'customer=Smith'];
        yield 'integer' => ['userConfiguration' => 1];
    }

    #[DataProvider('missingBackendUserDataProvider')]
    #[Test]
    public function getOrderSearchArgumentsReturnsAnEmptyArrayWithoutBackendUser(mixed $backendUser): void
    {
        $GLOBALS['BE_USER'] = $backendUser;

        self::assertSame(
            [],
            $this->subject->getOrderSearchArguments()
        );
    }

    #[DataProvider('missingBackendUserDataProvider')]
    #[Test]
    public function saveOrderSearchArgumentsDoesNothingWithoutBackendUser(mixed $backendUser): void
    {
        $this->backendUser->expects(self::never())->method('writeUC');
        $GLOBALS['BE_USER'] = $backendUser;

        $this->subject->saveOrderSearchArguments(self::ORDER_SEARCH_ARGUMENTS);

        self::assertSame([], $this->backendUser->uc);
    }

    /**
     * @return iterable<string, array{backendUser: mixed}>
     */
    public static function missingBackendUserDataProvider(): iterable
    {
        yield 'no backend user' => ['backendUser' => null];
        yield 'no instance of BackendUserAuthentication' => ['backendUser' => new stdClass()];
    }
}
