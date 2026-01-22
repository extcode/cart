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
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class CsvValuesViewHelperTest extends FunctionalTestCase
{
    use TestingFramework;

    private ItemRepository $itemRepository;

    public function setUp(): void
    {
        $this->testExtensionsToLoad[] = 'extcode/cart';

        parent::setUp();

        $this->itemRepository = GeneralUtility::makeInstance(ItemRepository::class);

        $querySettings = GeneralUtility::makeInstance(QuerySettingsInterface::class);
        $querySettings->setStoragePageIds([105]);
        $this->itemRepository->setDefaultQuerySettings($querySettings);

        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/Orders.php');
    }

    #[Test]
    public function orderWithEmptyOrderDataWithEmptyAddressDataExportsToCsvLine(): void
    {
        $orderItem = $this->itemRepository->findByUid(10);

        $template = __DIR__ . '/Fixtures/CsvValues.html';
        $view = $this->getView($template);
        $view->assign('orderItem', $orderItem);
        $content = $view->render();

        self::assertSame(
            ',,,,,,,' . "\n",
            $content
        );
    }

    #[Test]
    public function orderWithOrderDataAndWithAddressExportsToCsvLine(): void
    {
        $orderItem = $this->itemRepository->findByUid(11);

        $template = __DIR__ . '/Fixtures/CsvValues.html';
        $view = $this->getView($template);
        $view->assign('orderItem', $orderItem);
        $content = $view->render();

        self::assertSame(
            '"O-20260121-7","21.01.2026","I-20260122-3","22.01.2026","Mr",,"Arthur","Dent"' . "\n",
            $content
        );
    }

    #[Test]
    public function orderWithOrderDataAndWithAddressExportsToCsvLineWithDifferentDelimAndQuote(): void
    {
        $orderItem = $this->itemRepository->findByUid(11);

        $template = __DIR__ . '/Fixtures/CsvValuesWithDifferentDelimAndQuote.html';
        $view = $this->getView($template);
        $view->assign('orderItem', $orderItem);
        $content = $view->render();

        self::assertSame(
            '\'O-20260121-7\'|\'21.01.2026\'|\'I-20260122-3\'|\'22.01.2026\'|\'Mr\'||\'Arthur\'|\'Dent\'' . "\n",
            $content
        );
    }

    #[Test]
    public function orderWithOrderDataAndWithoutAddressExportsToCsvLine(): void
    {
        $orderItem = $this->itemRepository->findByUid(12);

        $template = __DIR__ . '/Fixtures/CsvValues.html';
        $view = $this->getView($template);
        $view->assign('orderItem', $orderItem);
        $content = $view->render();

        self::assertSame(
            '"O-20260121-7","21.01.2026","I-20260122-3","22.01.2026","","","",""' . "\n",
            $content
        );
    }

    private function getView(string $template): ViewInterface
    {
        $viewFactory = GeneralUtility::makeInstance(ViewFactoryInterface::class);
        return $viewFactory->create(new ViewFactoryData(null, null, null, $template));
    }
}
