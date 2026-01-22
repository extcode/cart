<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class CsvHeaderViewHelperTest extends FunctionalTestCase
{
    use TestingFramework;

    public function setUp(): void
    {
        $this->testExtensionsToLoad[] = 'extcode/cart';

        parent::setUp();
    }

    #[Test]
    public function headerExportsToCsvLine(): void
    {
        $template = __DIR__ . '/Fixtures/CsvHeader.html';
        $view = $this->getView($template);
        $content = $view->render();

        self::assertSame(
            '"Order Number","Order Date","Invoice Number","Invoice Date","Salutation","Title","FirstName","LastName"' . "\n",
            $content
        );
    }

    #[Test]
    public function headerExportsToCsvLineWithDifferentDelimAndQuote(): void
    {
        $template = __DIR__ . '/Fixtures/CsvHeaderWithDifferentDelimAndQuote.html';
        $view = $this->getView($template);
        $content = $view->render();

        self::assertSame(
            '\'Order Number\'|\'Order Date\'|\'Invoice Number\'|\'Invoice Date\'|\'Salutation\'|\'Title\'|\'FirstName\'|\'LastName\'' . "\n",
            $content
        );
    }

    private function getView(string $template): ViewInterface
    {
        $viewFactory = GeneralUtility::makeInstance(ViewFactoryInterface::class);
        return $viewFactory->create(new ViewFactoryData(null, null, null, $template));
    }
}
