<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\PageTitle\PageTitleProvider;
use Extcode\Cart\ViewHelpers\TitleTagViewHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(TitleTagViewHelper::class)]
final class TitleTagViewHelperTest extends FunctionalTestCase
{
    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'extcode/cart',
        ];

        parent::setUp();
    }

    #[Test]
    public function setsThePageTitle(): void
    {
        self::renderTitleTag('Product Title');

        self::assertSame('Product Title', $this->getPageTitleProvider()->getTitle());
    }

    #[Test]
    public function doesNotOverrideThePageTitleWithAnEmptyPageTitle(): void
    {
        $this->getPageTitleProvider()->setTitle('Existing Title');

        self::renderTitleTag('');

        self::assertSame('Existing Title', $this->getPageTitleProvider()->getTitle());
    }

    private function getPageTitleProvider(): PageTitleProvider
    {
        $pageTitleProvider = $this->get(PageTitleProvider::class);
        self::assertInstanceOf(PageTitleProvider::class, $pageTitleProvider);

        return $pageTitleProvider;
    }

    private static function renderTitleTag(string $pageTitle): void
    {
        $view = GeneralUtility::makeInstance(ViewFactoryInterface::class)
            ->create(new ViewFactoryData(null, null, null, __DIR__ . '/Fixtures/TitleTag.html'))
        ;
        $view->assign('pageTitle', $pageTitle);

        self::assertSame('', mb_trim($view->render()));
    }
}
