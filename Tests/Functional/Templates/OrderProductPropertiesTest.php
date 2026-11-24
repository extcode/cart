<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Templates;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Extcode\Cart\Domain\Model\Order\Item;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * The cart table of the order in the backend (partial Backend/Order/Show/Cart)
 * marks ordered products that are not shippable, not cancelable or not
 * returnable.
 */
final class OrderProductPropertiesTest extends FunctionalTestCase
{
    use TestingFramework;

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    #[Test]
    public function backendMarksProductsThatAreNotShippableCancelableOrReturnable(): void
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/OrderWithTaxes.php');
        $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_product')->update(
            'tx_cart_domain_model_order_product',
            ['shippable' => 0, 'cancelable' => 0, 'returnable' => 0],
            ['uid' => 2]
        );

        $orderItem = GeneralUtility::makeInstance(PersistenceManager::class)->getObjectByIdentifier(1, Item::class);
        self::assertInstanceOf(Item::class, $orderItem);

        self::assertSame(
            [
                'Book' => [],
                'Shirt' => ['not shippable', 'not cancelable', 'not returnable'],
            ],
            self::getMarksPerProduct(self::render($orderItem))
        );
    }

    private static function render(Item $orderItem): string
    {
        $extbaseRequestParameters = new ExtbaseRequestParameters();
        $extbaseRequestParameters->setControllerExtensionName('Cart');
        $request = new Request(
            (new ServerRequest('https://example.com/'))
                ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
                ->withAttribute('language', new SiteLanguage(0, 'en_US.UTF-8', new Uri('/'), []))
                ->withAttribute('extbase', $extbaseRequestParameters)
        );

        $view = GeneralUtility::makeInstance(ViewFactoryInterface::class)->create(
            new ViewFactoryData(
                templateRootPaths: ['EXT:cart/Tests/Functional/Fixtures/Templates/'],
                partialRootPaths: ['EXT:cart/Resources/Private/Partials/'],
                layoutRootPaths: ['EXT:cart/Resources/Private/Layouts/'],
                request: $request,
            )
        );
        $view->assign('orderItem', $orderItem);

        return $view->render('Backend/Order/Cart');
    }

    /**
     * @return array<string, list<string>> the marks per product title
     */
    private static function getMarksPerProduct(string $html): array
    {
        $document = new DOMDocument();
        $document->loadHTML('<?xml encoding="utf-8"?>' . $html, LIBXML_NOERROR);
        $xpath = new DOMXPath($document);

        $marks = [];
        foreach ($xpath->query('//tr/td[2]') ?: [] as $cell) {
            self::assertInstanceOf(DOMElement::class, $cell);
            $title = trim((string) $cell->firstChild?->textContent);
            if (!in_array($title, ['Book', 'Shirt'], true)) {
                continue;
            }
            $marks[$title] = [];
            foreach ($xpath->query('.//span[@data-order-product-property]', $cell) ?: [] as $mark) {
                self::assertInstanceOf(DOMElement::class, $mark);
                $marks[$title][] = trim($mark->textContent);
            }
        }

        return $marks;
    }
}
