<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Templates;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
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
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class OrderNumberTest extends FunctionalTestCase
{
    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'extcode/cart',
        ];

        parent::setUp();
    }

    #[DataProvider('templatesProvider')]
    #[Test]
    public function rendersTheOrderNumber(string $template, string $variableName): void
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
                templateRootPaths: ['EXT:cart/Resources/Private/Templates/'],
                partialRootPaths: ['EXT:cart/Resources/Private/Partials/'],
                layoutRootPaths: ['EXT:cart/Resources/Private/Layouts/'],
                request: $request,
            )
        );
        $view->assign($variableName, ['orderNumber' => '2026-00042']);

        self::assertStringContainsString(
            'Your order number is: 2026-00042',
            $view->render($template)
        );
    }

    /**
     * @return Generator<string, array{string, string}>
     */
    public static function templatesProvider(): iterable
    {
        yield 'order create' => ['Cart/Order/Create', 'cart'];
        yield 'order show' => ['Cart/Order/Show', 'orderItem'];
        yield 'order finished' => ['Cart/OrderFinished', 'orderItem'];
    }
}
