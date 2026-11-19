<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Closure;
use Codappix\Typo3PhpDatasets\TestingFramework;
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Event\Order\ExportEvent;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class OrderControllerTest extends FunctionalTestCase
{
    use BackendModuleTrait;

    use TestingFramework;

    /**
     * Page uid of the sysfolder holding the orders, see Tests/Fixtures/BaseDatabase.php
     */
    private const ORDER_STORAGE_PID = 105;

    private const NUMBER_OF_ORDERS = 100;

    private const ITEMS_PER_PAGE = 20;

    /**
     * Page uid of the root page of the order storage, see Tests/Fixtures/BaseDatabase.php
     */
    private const SHOP_ROOT_PID = 101;

    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'extcode/cart',
        ];

        $this->coreExtensionsToLoad = [
            'typo3/cms-beuser',
            'typo3/cms-core',
        ];

        $this->pathsToLinkInTestInstance['typo3conf/ext/cart/Tests/Functional/Fixtures/Import/Sites/'] = 'typo3conf/sites';

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/BackendUser.php');

        $this->setUpBackendUser(1);

        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('en');
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['LANG'],
            $GLOBALS['TYPO3_REQUEST']
        );

        parent::tearDown();
    }

    #[Test]
    public function listActionRendersOnlyItemsPerPageOrdersOfHundredOrders(): void
    {
        $this->createOrders(self::NUMBER_OF_ORDERS);

        $html = (string) self::dispatchListAction()->getBody();

        self::assertCount(
            self::ITEMS_PER_PAGE,
            self::getRenderedOrderNumbers($html)
        );
    }

    #[DataProvider('pageDataProvider')]
    #[Test]
    public function listActionRendersOrdersOfRequestedPage(
        int $currentPage,
        int $firstOrderNumber,
        int $lastOrderNumber,
        int $firstRecordNumber,
        int $lastRecordNumber
    ): void {
        $this->createOrders(self::NUMBER_OF_ORDERS);

        $html = (string) self::dispatchListAction($currentPage)->getBody();

        $expectedOrderNumbers = array_map(
            self::buildOrderNumber(...),
            range($firstOrderNumber, $lastOrderNumber)
        );

        self::assertSame(
            $expectedOrderNumbers,
            self::getRenderedOrderNumbers($html)
        );

        self::assertStringContainsString(
            'Records ' . $firstRecordNumber . ' - ' . $lastRecordNumber,
            self::normalizeWhitespace($html)
        );
    }

    /**
     * @return iterable<string, array{currentPage: int, firstOrderNumber: int, lastOrderNumber: int, firstRecordNumber: int, lastRecordNumber: int}>
     */
    public static function pageDataProvider(): iterable
    {
        yield 'first page' => [
            'currentPage' => 1,
            'firstOrderNumber' => 100,
            'lastOrderNumber' => 81,
            'firstRecordNumber' => 1,
            'lastRecordNumber' => 20,
        ];
        yield 'third page' => [
            'currentPage' => 3,
            'firstOrderNumber' => 60,
            'lastOrderNumber' => 41,
            'firstRecordNumber' => 41,
            'lastRecordNumber' => 60,
        ];
        yield 'last page' => [
            'currentPage' => 5,
            'firstOrderNumber' => 20,
            'lastOrderNumber' => 1,
            'firstRecordNumber' => 81,
            'lastRecordNumber' => 100,
        ];
    }

    #[Test]
    public function listActionRendersPaginationWithAllPages(): void
    {
        $this->createOrders(self::NUMBER_OF_ORDERS);

        $html = self::normalizeWhitespace((string) self::dispatchListAction()->getBody());

        $numberOfPages = (int) ceil(self::NUMBER_OF_ORDERS / self::ITEMS_PER_PAGE);

        preg_match('/<ul class="pagination">(.*)<\/ul>/', $html, $matches);
        self::assertArrayHasKey(1, $matches, 'No pagination has been rendered.');

        preg_match_all('/class="page-link">(\d+)<\/a>/', $matches[1], $pageMatches);

        self::assertSame(
            array_map(strval(...), range(1, $numberOfPages)),
            $pageMatches[1]
        );
    }

    #[Test]
    public function listActionMarksRequestedPageAsActiveInPagination(): void
    {
        $this->createOrders(self::NUMBER_OF_ORDERS);

        $html = self::normalizeWhitespace((string) self::dispatchListAction(3)->getBody());

        self::assertMatchesRegularExpression(
            '/<li class="page-item active"> <a href="[^"]*" class="page-link">3<\/a>/',
            $html
        );
    }

    /**
     * @param string[] $expectedOrderNumbers
     */
    #[DataProvider('customerDataProvider')]
    #[Test]
    public function listActionRendersOnlyTheOrdersOfTheCustomerGivenAsSearchArgument(
        string $customer,
        array $expectedOrderNumbers
    ): void {
        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/Orders.php');

        $html = self::normalizeWhitespace(
            (string) self::dispatchListAction(searchArguments: ['customer' => $customer])->getBody()
        );

        self::assertSame(
            $expectedOrderNumbers,
            self::getRenderedOrderNumbers($html),
            'Only the orders of the searched customer must be rendered, the latest one first.'
        );

        self::assertStringContainsString(
            'Records 1 - ' . count($expectedOrderNumbers),
            $html
        );
    }

    /**
     * The customers and their orders are taken from Tests/Fixtures/Orders.php,
     * each of the names is used by exactly one customer.
     *
     * @return iterable<string, array{customer: string, expectedOrderNumbers: string[]}>
     */
    public static function customerDataProvider(): iterable
    {
        yield 'customer with one order' => [
            'customer' => 'Smith',
            'expectedOrderNumbers' => [
                'O-20260222-1',
            ],
        ];
        yield 'customer with two orders' => [
            'customer' => 'Wolf',
            'expectedOrderNumbers' => [
                'O-20260823-1',
                'O-20260225-1',
            ],
        ];
        yield 'customer with four orders' => [
            'customer' => 'Everdeen',
            'expectedOrderNumbers' => [
                'O-20260530-1',
                'O-20260504-2',
                'O-20260501-3',
                'O-20260501-1',
            ],
        ];
    }

    #[Test]
    public function listActionResetsTheSavedSearchArgumentsWhenTheFilterFormIsSubmittedEmpty(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/Orders.php');

        $unfilteredOrderNumbers = self::getRenderedOrderNumbers(
            (string) self::dispatchListAction()->getBody()
        );
        self::assertCount(self::ITEMS_PER_PAGE, $unfilteredOrderNumbers);

        $filteredOrderNumbers = self::getRenderedOrderNumbers(
            (string) self::dispatchListAction(searchArguments: ['customer' => 'Smith'])->getBody()
        );
        self::assertSame(['O-20260222-1'], $filteredOrderNumbers);

        // the filter form sends all of its fields, the empty ones as empty strings
        $resetOrderNumbers = self::getRenderedOrderNumbers(
            (string) self::dispatchListAction(searchArguments: ['customer' => '', 'orderNumber' => ''])->getBody()
        );
        self::assertSame(
            $unfilteredOrderNumbers,
            $resetOrderNumbers,
            'An empty filter form must reset the saved search arguments.'
        );

        // e.g. when returning from another module
        $orderNumbersOfFollowingRequest = self::getRenderedOrderNumbers(
            (string) self::dispatchListAction()->getBody()
        );
        self::assertSame(
            $unfilteredOrderNumbers,
            $orderNumbersOfFollowingRequest,
            'The reset search arguments must not be restored by a following request.'
        );
    }

    #[Test]
    public function listActionIgnoresUnknownSearchArgumentsAndSearchArgumentsWithoutScalarValue(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/Orders.php');

        $unfilteredOrderNumbers = self::getRenderedOrderNumbers(
            (string) self::dispatchListAction()->getBody()
        );

        $html = (string) self::dispatchRequestWithQueryParameters([
            'id' => self::ORDER_STORAGE_PID,
            'search' => [
                'customer' => ['Smith'],
                'unknownArgument' => 'Smith',
            ],
        ])->getBody();

        self::assertSame(
            $unfilteredOrderNumbers,
            self::getRenderedOrderNumbers($html)
        );
    }

    #[Test]
    public function listActionRemovesTheSavedSearchArgumentsWhenFollowingTheResetLink(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/Orders.php');

        $unfilteredOrderNumbers = self::getRenderedOrderNumbers(
            (string) self::dispatchListAction()->getBody()
        );
        self::assertCount(self::ITEMS_PER_PAGE, $unfilteredOrderNumbers);

        $filteredPage = (string) self::dispatchListAction(searchArguments: ['customer' => 'Smith'])->getBody();
        self::assertSame(['O-20260222-1'], self::getRenderedOrderNumbers($filteredPage));
        self::assertSame('Smith', self::getValueOfCustomerSearchField($filteredPage));

        // the request only uses the reset link of the filter form, like a click in the backend module does
        $resetPage = (string) self::dispatchRequestWithQueryParameters(
            self::getQueryParametersOfResetLink($filteredPage)
        )->getBody();
        self::assertSame(
            $unfilteredOrderNumbers,
            self::getRenderedOrderNumbers($resetPage),
            'The reset link must remove the filter of the order list.'
        );
        self::assertSame(
            '',
            self::getValueOfCustomerSearchField($resetPage),
            'The reset link must clear the filter form.'
        );

        // e.g. when returning from another module
        self::assertSame(
            $unfilteredOrderNumbers,
            self::getRenderedOrderNumbers((string) self::dispatchListAction()->getBody()),
            'The reset link must remove the search arguments from the user configuration.'
        );
    }

    #[Test]
    public function listActionRendersTheResetLinkOnlyIfThereIsAnActiveSearch(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/Orders.php');

        self::assertFalse(
            self::hasResetLink((string) self::dispatchListAction()->getBody()),
            'Without a search the reset link must not be rendered.'
        );

        $filteredPage = (string) self::dispatchListAction(searchArguments: ['customer' => 'Smith'])->getBody();
        self::assertTrue(
            self::hasResetLink($filteredPage),
            'With a search the reset link must be rendered.'
        );

        $resetPage = (string) self::dispatchRequestWithQueryParameters(
            self::getQueryParametersOfResetLink($filteredPage)
        )->getBody();
        self::assertFalse(
            self::hasResetLink($resetPage),
            'After following the reset link the reset link must not be rendered.'
        );

        // the filter form sends all of its fields, the empty ones as empty strings, "all" of the status selects as well
        $emptyFormPage = (string) self::dispatchListAction(searchArguments: [
            'customer' => '',
            'orderNumber' => '',
            'orderDateStart' => '',
            'orderDateEnd' => '',
            'invoiceNumber' => '',
            'invoiceDateStart' => '',
            'invoiceDateEnd' => '',
            'paymentStatus' => '',
            'shippingStatus' => '',
        ])->getBody();
        self::assertFalse(
            self::hasResetLink($emptyFormPage),
            'After submitting an empty filter form the reset link must not be rendered.'
        );
    }

    #[Test]
    public function listActionRendersOnlyTheOrdersOfTheMonthGivenAsOrderNumberSearchArgumentOnAllPages(): void
    {
        // allows to test several pages with the 24 orders of May 2026 in Tests/Fixtures/Orders.php
        $itemsPerPage = 10;

        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/Orders.php');
        $this->setItemsPerPage($itemsPerPage);

        // May 2026 is the month with the most orders in Tests/Fixtures/Orders.php, the latest one first
        $searchArguments = ['orderNumber' => 'O-202605%'];
        $expectedOrderNumbersOfFirstPage = [
            'O-20260530-1',
            'O-20260528-1',
            'O-20260527-4',
            'O-20260527-3',
            'O-20260527-2',
            'O-20260527-1',
            'O-20260526-1',
            'O-20260524-1',
            'O-20260519-4',
            'O-20260519-3',
        ];
        $expectedOrderNumbersOfSecondPage = [
            'O-20260519-2',
            'O-20260519-1',
            'O-20260518-2',
            'O-20260518-1',
            'O-20260516-3',
            'O-20260516-2',
            'O-20260516-1',
            'O-20260513-1',
            'O-20260507-1',
            'O-20260504-2',
        ];
        $expectedOrderNumbersOfThirdPage = [
            'O-20260504-1',
            'O-20260501-3',
            'O-20260501-2',
            'O-20260501-1',
        ];

        $firstPage = self::normalizeWhitespace(
            (string) self::dispatchListAction(1, $searchArguments)->getBody()
        );
        // the following requests only use the links of the pagination, like a click in the backend module does
        $secondPage = self::normalizeWhitespace(
            (string) self::dispatchRequestWithQueryParameters(
                self::getQueryParametersOfPaginationLink($firstPage, 2)
            )->getBody()
        );
        $thirdPage = self::normalizeWhitespace(
            (string) self::dispatchRequestWithQueryParameters(
                self::getQueryParametersOfPaginationLink($secondPage, 3)
            )->getBody()
        );

        self::assertSame(
            $expectedOrderNumbersOfFirstPage,
            self::getRenderedOrderNumbers($firstPage),
            'Only the orders of the searched month must be rendered on page 1.'
        );
        self::assertStringContainsString('Records 1 - 10', $firstPage);

        self::assertSame(
            $expectedOrderNumbersOfSecondPage,
            self::getRenderedOrderNumbers($secondPage),
            'The pagination link to page 2 must keep the search arguments of the current list.'
        );
        self::assertStringContainsString('Records 11 - 20', $secondPage);

        self::assertSame(
            $expectedOrderNumbersOfThirdPage,
            self::getRenderedOrderNumbers($thirdPage),
            'The pagination link to page 3 must keep the search arguments of the current list.'
        );
        self::assertStringContainsString('Records 21 - 24', $thirdPage);
    }

    #[Test]
    public function exportActionRendersCsvWithOneLinePerOrder(): void
    {
        $this->createOrders(3);

        $response = self::dispatchExportAction();

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('text/csv', $response->getHeaderLine('Content-Type'));
        self::assertMatchesRegularExpression(
            '/^attachment; filename="Order-Export-\d{4}-\d{2}-\d{2}_\d{2}-\d{2}\.csv"$/',
            $response->getHeaderLine('Content-Disposition')
        );
        self::assertSame(
            implode("\n", [
                '"Order Number","Order Date","Invoice Number","Invoice Date","Salutation","Title","FirstName","LastName"',
                '"O-003","21.01.2026",,,,,"Arthur","Dent"',
                '"O-002","21.01.2026",,,,,"Arthur","Dent"',
                '"O-001","21.01.2026",,,,,"Arthur","Dent"',
                '',
            ]),
            (string) $response->getBody()
        );
    }

    #[Test]
    public function exportActionExportsOnlyTheOrdersOfTheSearchOfTheOrderList(): void
    {
        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/Orders.php');

        $filteredPage = (string) self::dispatchListAction(searchArguments: ['customer' => 'Wolf'])->getBody();
        self::assertSame(['O-20260823-1', 'O-20260225-1'], self::getRenderedOrderNumbers($filteredPage));

        // the request only uses the export link of the button bar, like a click in the backend module does
        $csv = (string) self::dispatchRequestWithQueryParameters(
            self::getQueryParametersOfExportLink($filteredPage),
            'cart_cart_orders.Backend\Order\Order_export'
        )->getBody();

        self::assertSame(
            ['O-20260823-1', 'O-20260225-1'],
            self::getExportedOrderNumbers($csv),
            'The export must only contain the orders of the search of the order list.'
        );
    }

    #[Test]
    public function exportActionReturnsTheResponseOfAnEventListenerForAnotherFormat(): void
    {
        $this->createOrders(3);

        $this->addExportEventListener(
            static function (ExportEvent $event): void {
                if ($event->getFormat() !== 'xml') {
                    return;
                }

                $response = new Response();
                $response->getBody()->write('<orders/>');

                $event->setResponse($response->withHeader('Content-Type', 'text/xml'));
            }
        );

        $response = self::dispatchExportAction(['format' => 'xml']);

        self::assertSame('text/xml', $response->getHeaderLine('Content-Type'));
        self::assertSame('<orders/>', (string) $response->getBody());
    }

    #[Test]
    public function exportActionKeepsTheResponseOfTheFluidTemplateForCsv(): void
    {
        $this->createOrders(3);

        $this->addExportEventListener(
            static function (ExportEvent $event): void {
                $event->setResponse(new Response());
            }
        );

        $response = self::dispatchExportAction();

        self::assertSame('text/csv', $response->getHeaderLine('Content-Type'));
        self::assertSame(
            ['O-003', 'O-002', 'O-001'],
            self::getExportedOrderNumbers((string) $response->getBody()),
            'The EventListener of cart renders the template and stops the propagation of the event.'
        );
    }

    #[Test]
    public function exportActionThrowsExceptionIfNoEventListenerIsResponsibleForTheFormat(): void
    {
        $this->expectExceptionObject(
            new RuntimeException(
                'There is no EventListener for the ExportEvent, which exports the orders in the format "json".',
                1794833771
            )
        );

        self::dispatchExportAction(['format' => 'json']);
    }

    private function addExportEventListener(Closure $listener): void
    {
        $container = $this->getContainer();
        assert($container instanceof Container);

        $container->set('cart-test--event-listener--order--export', $listener);
        GeneralUtility::makeInstance(ListenerProvider::class)->addListener(
            ExportEvent::class,
            'cart-test--event-listener--order--export'
        );
    }

    /**
     * @param array<array-key, mixed> $queryParameters
     */
    private static function dispatchExportAction(array $queryParameters = []): ResponseInterface
    {
        return self::dispatchRequestWithQueryParameters(
            array_merge(['id' => self::ORDER_STORAGE_PID, 'format' => 'csv'], $queryParameters),
            'cart_cart_orders.Backend\Order\Order_export'
        );
    }

    /**
     * @param array<string, string> $searchArguments the filters of OrderController::listAction
     */
    private static function dispatchListAction(int $currentPage = 1, array $searchArguments = []): ResponseInterface
    {
        $queryParameters = [
            'id' => self::ORDER_STORAGE_PID,
            'currentPage' => $currentPage,
        ];

        if ($searchArguments !== []) {
            $queryParameters['search'] = $searchArguments;
        }

        return self::dispatchRequestWithQueryParameters($queryParameters);
    }

    /**
     * @return string[] the order numbers in the order they are rendered in the list
     */
    private static function getRenderedOrderNumbers(string $html): array
    {
        // the filter renders the search arguments as well, only the order list is of interest here
        $orderList = mb_strstr($html, '<table') ?: '';

        preg_match_all('/O-\d+(?:-\d+)?/', $orderList, $matches);

        return $matches[0];
    }

    /**
     * @return array<array-key, mixed> the query parameters of the link to the given page in the pagination
     */
    private static function getQueryParametersOfPaginationLink(string $html, int $page): array
    {
        preg_match('/<a href="([^"]*)" class="page-link">' . $page . '<\/a>/', $html, $matches);
        self::assertArrayHasKey(1, $matches, 'No pagination link to page ' . $page . ' has been rendered.');

        return self::getQueryParametersOfHref($matches[1]);
    }

    /**
     * @return array<array-key, mixed> the query parameters of the CSV export button of the button bar
     */
    private static function getQueryParametersOfExportLink(string $html): array
    {
        preg_match('/<a [^>]*href="([^"]*\/export\?[^"]*)"/', $html, $matches);
        self::assertArrayHasKey(1, $matches, 'No export link has been rendered.');

        return self::getQueryParametersOfHref($matches[1]);
    }

    /**
     * @return string[] the order numbers in the order they are exported
     */
    private static function getExportedOrderNumbers(string $csv): array
    {
        preg_match_all('/^"(O-[^"]+)"/m', $csv, $matches);

        return $matches[1];
    }

    private static function hasResetLink(string $html): bool
    {
        return preg_match('/<a [^>]*id="searchReset"[^>]*>/', $html) === 1;
    }

    /**
     * @return array<array-key, mixed> the query parameters of the reset link of the filter form
     */
    private static function getQueryParametersOfResetLink(string $html): array
    {
        preg_match('/<a [^>]*id="searchReset"[^>]*>/', $html, $linkMatches);
        self::assertArrayHasKey(0, $linkMatches, 'No reset link of the filter form has been rendered.');

        preg_match('/href="([^"]*)"/', $linkMatches[0], $hrefMatches);
        self::assertArrayHasKey(1, $hrefMatches, 'The reset link of the filter form has no href.');

        return self::getQueryParametersOfHref($hrefMatches[1]);
    }

    /**
     * @return array<array-key, mixed>
     */
    private static function getQueryParametersOfHref(string $href): array
    {
        $query = (string) parse_url(html_entity_decode($href), PHP_URL_QUERY);
        parse_str($query, $queryParameters);

        return $queryParameters;
    }

    private static function getValueOfCustomerSearchField(string $html): string
    {
        preg_match('/<input [^>]*id="searchCustomer"[^>]*>/', $html, $inputMatches);
        self::assertArrayHasKey(0, $inputMatches, 'No customer field of the filter form has been rendered.');

        preg_match('/value="([^"]*)"/', $inputMatches[0], $valueMatches);

        return html_entity_decode($valueMatches[1] ?? '');
    }

    private static function normalizeWhitespace(string $html): string
    {
        return mb_trim((string) preg_replace('/\s+/', ' ', $html));
    }

    private static function buildOrderNumber(int $number): string
    {
        return sprintf('O-%03d', $number);
    }

    /**
     * The backend module reads module.tx_cart.settings from the TypoScript of the rootline of the order storage.
     */
    private function setItemsPerPage(int $itemsPerPage): void
    {
        $this->getConnectionPool()->getConnectionForTable('sys_template')->insert(
            'sys_template',
            [
                'uid' => 1,
                'pid' => self::SHOP_ROOT_PID,
                'title' => 'Shop',
                'root' => 1,
                'clear' => 3,
                'config' => 'module.tx_cart.settings.itemsPerPage = ' . $itemsPerPage,
            ]
        );
    }

    private function createOrders(int $numberOfOrders): void
    {
        $connectionPool = $this->getConnectionPool();
        $itemConnection = $connectionPool->getConnectionForTable('tx_cart_domain_model_order_item');
        $addressConnection = $connectionPool->getConnectionForTable('tx_cart_domain_model_order_address');

        for ($number = 1; $number <= $numberOfOrders; $number++) {
            $itemConnection->insert(
                'tx_cart_domain_model_order_item',
                [
                    'uid' => $number,
                    'pid' => self::ORDER_STORAGE_PID,
                    'billing_address' => $number,
                    'order_number' => self::buildOrderNumber($number),
                    'order_date' => 1769034161,
                    'currency' => '€',
                    'total_gross' => 119.0,
                    'total_net' => 100.0,
                ]
            );

            $addressConnection->insert(
                'tx_cart_domain_model_order_address',
                [
                    'uid' => $number,
                    'pid' => self::ORDER_STORAGE_PID,
                    'item' => $number,
                    'record_type' => '\\' . BillingAddress::class,
                    'first_name' => 'Arthur',
                    'last_name' => 'Dent',
                ]
            );
        }
    }
}
