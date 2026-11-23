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
 * and in the frontend (partial Order/Show/Cart) renders one column per tax
 * class of the order with the taxes of the products and the services in the
 * column of their tax class. The tax class of a discount is not stored, its
 * row has empty tax columns.
 */
final class OrderTaxColumnsTest extends FunctionalTestCase
{
    use TestingFramework;

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    #[Test]
    public function backendRendersTheTaxesInTheColumnOfTheirTaxClass(): void
    {
        self::assertSame(
            [
                ['VAT (19 %)', 'VAT (7 %)'],
                ['', '0,70€'],
                ['3,80€', ''],
                ['3,80€', '0,70€'],
                ['0,95€', ''],
                ['0,19€', ''],
                ['', ''],
                ['3,04€', '0,70€'],
            ],
            self::getTaxColumns(self::render('Backend/Order/Cart', $this->loadOrderItem()))
        );
    }

    #[Test]
    public function frontendRendersTheTaxesInTheColumnOfTheirTaxClass(): void
    {
        self::assertSame(
            [
                ['VAT (19 %)', 'VAT (7 %)'],
                ['', '0,70€'],
                ['3,80€', ''],
                ['3,80€', '0,70€'],
                ['0,95€', ''],
                ['0,19€', ''],
                ['3,04€', '0,70€'],
            ],
            self::getTaxColumns(self::render('Order/Cart', $this->loadOrderItem()))
        );
    }

    private function loadOrderItem(): Item
    {
        $this->importPHPDataSet(__DIR__ . '/Fixtures/OrderWithTaxes.php');

        $orderItem = GeneralUtility::makeInstance(PersistenceManager::class)->getObjectByIdentifier(1, Item::class);
        self::assertInstanceOf(Item::class, $orderItem);

        return $orderItem;
    }

    private static function render(string $template, Item $orderItem): string
    {
        $extbaseRequestParameters = new ExtbaseRequestParameters();
        $extbaseRequestParameters->setControllerExtensionName('Cart');
        $request = new Request(
            (new ServerRequest('https://example.com/'))
                ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
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
        $view->assign('settings', [
            'format' => [
                'currency' => [
                    'decimalSeparator' => ',',
                    'thousandsSeparator' => '.',
                    'decimals' => '2',
                ],
            ],
        ]);
        $view->assign('orderItem', $orderItem);

        return $view->render($template);
    }

    /**
     * @return list<list<string>> the content of the last two cells (the tax columns) of each row
     */
    private static function getTaxColumns(string $html): array
    {
        $document = new DOMDocument();
        $document->loadHTML('<?xml encoding="utf-8"?>' . $html, LIBXML_NOERROR);

        $rows = [];
        foreach ((new DOMXPath($document))->query('//tr') ?: [] as $row) {
            self::assertInstanceOf(DOMElement::class, $row);
            $cells = [];
            foreach ($row->childNodes as $cell) {
                if ($cell instanceof DOMElement && in_array($cell->tagName, ['td', 'th'], true)) {
                    $cells[] = trim($cell->textContent, " \t\n\r\0\x0B\u{a0}");
                }
            }
            $rows[] = array_slice($cells, -2);
        }

        return $rows;
    }
}
