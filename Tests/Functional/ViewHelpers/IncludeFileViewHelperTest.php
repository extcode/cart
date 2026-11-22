<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\ViewHelpers\IncludeFileViewHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(IncludeFileViewHelper::class)]
final class IncludeFileViewHelperTest extends FunctionalTestCase
{
    private ServerRequestInterface $request;

    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'extcode/cart',
        ];

        parent::setUp();

        $request = (new ServerRequest('https://example.com/', 'GET', null, [], ['HTTP_HOST' => 'example.com', 'HTTPS' => 'on']))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
        ;
        $this->request = $request
            ->withAttribute('normalizedParams', NormalizedParams::createFromRequest($request))
        ;
        $GLOBALS['TYPO3_REQUEST'] = $this->request;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        parent::tearDown();
    }

    #[DataProvider('includedFileDataProvider')]
    #[Test]
    public function includesAFile(string $path, string $expectedTag): void
    {
        self::renderIncludeFile($path);

        self::assertStringContainsString($expectedTag, $this->renderPage());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function includedFileDataProvider(): iterable
    {
        yield 'css' => ['EXT:cart/Resources/Public/Stylesheets/cart.css', '<link rel="stylesheet" href="/typo3conf/ext/cart/Resources/Public/Stylesheets/cart.css?'];
        yield 'js' => ['EXT:cart/Resources/Public/JavaScript/add_to_cart.js', '<script src="/typo3conf/ext/cart/Resources/Public/JavaScript/add_to_cart.js?'];
        yield 'url' => ['https://example.com/styles.css', '<link rel="stylesheet" href="https://example.com/styles.css"'];
    }

    #[DataProvider('invalidPathDataProvider')]
    #[Test]
    public function ignoresAnInvalidPath(string $path): void
    {
        self::renderIncludeFile($path);

        self::assertStringNotContainsString('does-not-exist', $this->renderPage());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidPathDataProvider(): iterable
    {
        yield 'path with ../' => ['../does-not-exist.css'];
        yield 'path outside of the allowed paths' => ['/etc/does-not-exist.css'];
    }

    private function renderPage(): string
    {
        $pageRenderer = $this->get(PageRenderer::class);
        self::assertInstanceOf(PageRenderer::class, $pageRenderer);

        return (string) $pageRenderer->renderResponse($this->request)->getBody();
    }

    private static function renderIncludeFile(string $path): void
    {
        $view = GeneralUtility::makeInstance(ViewFactoryInterface::class)
            ->create(new ViewFactoryData(null, null, null, __DIR__ . '/Fixtures/IncludeFile.html'))
        ;
        $view->assign('path', $path);

        self::assertSame('', mb_trim($view->render()));
    }
}
