<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Backend\Middleware\BackendModuleValidator;
use TYPO3\CMS\Backend\Routing\Router;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class DocumentControllerTest extends FunctionalTestCase
{
    use TestingFramework;

    /**
     * Page uid of the sysfolder holding the orders, see Tests/Fixtures/BaseDatabase.php
     */
    private const ORDER_STORAGE_PID = 105;

    /**
     * Order with order and invoice number, but without delivery number, see Tests/Fixtures/Orders.php
     */
    private const ORDER_ITEM_UID = 11;

    private const CREATE_ROUTE = 'cart_cart_orders.Backend\Order\Document_create';

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
        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/Orders.php');

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
    public function createActionShowsTheGeneratedNumberOfTheTypeOfTheDocument(): void
    {
        $response = self::dispatchRequestWithQueryParameters(
            [
                'id' => self::ORDER_STORAGE_PID,
                'orderItem' => self::ORDER_ITEM_UID,
                'pdfType' => 'delivery',
            ],
            self::CREATE_ROUTE
        );

        self::assertSame(303, $response->getStatusCode());

        $deliveryNumber = $this->getConnectionPool()
            ->getConnectionForTable('tx_cart_domain_model_order_item')
            ->select(['delivery_number'], 'tx_cart_domain_model_order_item', ['uid' => self::ORDER_ITEM_UID])
            ->fetchOne()
        ;
        self::assertSame('1', $deliveryNumber);

        self::assertContains(
            'The generated delivery number is: 1.',
            self::getFlashMessages(),
            'The message must show the generated delivery number, not the invoice number of the order.'
        );
    }

    /**
     * @return string[] the texts of the flash messages of the orders backend module
     */
    private static function getFlashMessages(): array
    {
        return array_map(
            static fn(FlashMessage $flashMessage): string => $flashMessage->getMessage(),
            GeneralUtility::makeInstance(FlashMessageService::class)
                ->getMessageQueueByIdentifier('extbase.flashmessages.tx_cart_cart_cart_orders')
                ->getAllMessages()
        );
    }

    /**
     * @param array<array-key, mixed> $queryParameters
     */
    private static function dispatchRequestWithQueryParameters(array $queryParameters, string $routeIdentifier): ResponseInterface
    {
        $request = self::buildRequest($queryParameters, $routeIdentifier);

        // the middleware adds the module data to the request
        return GeneralUtility::makeInstance(BackendModuleValidator::class)->process(
            $request,
            new class implements RequestHandlerInterface {
                public function handle(ServerRequestInterface $request): ResponseInterface
                {
                    $GLOBALS['TYPO3_REQUEST'] = $request;

                    return GeneralUtility::makeInstance(Bootstrap::class)->handleBackendRequest($request);
                }
            }
        );
    }

    /**
     * @param array<array-key, mixed> $queryParameters
     */
    private static function buildRequest(array $queryParameters, string $routeIdentifier): ServerRequestInterface
    {
        $route = GeneralUtility::makeInstance(Router::class)->getRoute($routeIdentifier);
        self::assertNotNull($route, 'The route ' . $routeIdentifier . ' of the backend module is not registered.');

        $queryParameters['route'] = $route->getPath();

        $request = (new ServerRequest(
            'https://localhost/typo3' . $route->getPath() . '?' . http_build_query($queryParameters),
            'GET'
        ))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withAttribute('route', $route)
            ->withAttribute('module', $route->getOption('module'))
            ->withQueryParams($queryParameters)
        ;

        return $request->withAttribute('normalizedParams', NormalizedParams::createFromRequest($request));
    }
}
