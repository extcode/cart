<?php

namespace Extcode\Cart\Tests\Unit\Controller;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Controller\Cart\ProductController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Traversable;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ProductController::class)]
class ProductControllerTest extends UnitTestCase
{
    #[DataProvider('getHighestSeverityDataProvider')]
    #[Test]
    public function getHighestSeverity(array $errors, ContextualFeedbackSeverity $expectedSeverity): void
    {
        $productController = GeneralUtility::makeInstance(ProductController::class);

        $reflection = new \ReflectionClass(ProductController::class);
        $method = $reflection->getMethod('getErrorWithHighestSeverity');
        $error = $method->invoke($productController, $errors);

        self::assertSame(
            $expectedSeverity,
            $error->getSeverity()
        );
    }

    public static function getHighestSeverityDataProvider(): Traversable
    {
        yield [
            'errors' => [
                new FlashMessage(
                    'OK',
                    'OK',
                    ContextualFeedbackSeverity::OK
                ),
                new FlashMessage(
                    'WARNING',
                    'WARNING',
                    ContextualFeedbackSeverity::WARNING
                ),
            ],
            'expectedSeverity' => ContextualFeedbackSeverity::WARNING,
        ];
        yield [
            'errors' => [
                new FlashMessage(
                    'OK',
                    'OK',
                    ContextualFeedbackSeverity::OK
                ),
                new FlashMessage(
                    'WARNING',
                    'WARNING',
                    ContextualFeedbackSeverity::ERROR
                ),
            ],
            'expectedSeverity' => ContextualFeedbackSeverity::ERROR,
        ];
        yield [
            'errors' => [
                new FlashMessage(
                    'OK',
                    'OK',
                    ContextualFeedbackSeverity::WARNING
                ),
                new FlashMessage(
                    'WARNING',
                    'WARNING',
                    ContextualFeedbackSeverity::ERROR
                ),
            ],
            'expectedSeverity' => ContextualFeedbackSeverity::ERROR,
        ];
        yield [
            'errors' => [
                new FlashMessage(
                    'OK',
                    'OK',
                    ContextualFeedbackSeverity::OK
                ),
                new FlashMessage(
                    'ERROR',
                    'ERROR',
                    ContextualFeedbackSeverity::ERROR
                ),
                new FlashMessage(
                    'WARNING',
                    'WARNING',
                    ContextualFeedbackSeverity::WARNING
                ),
            ],
            'expectedSeverity' => ContextualFeedbackSeverity::ERROR,
        ];
    }

    #[DataProvider('getLastHighestSeverityDataProvider')]
    #[Test]
    public function getLastHighestSeverity(array $errors, ContextualFeedbackSeverity $expectedSeverity, string $expectedMessage): void
    {
        $productController = GeneralUtility::makeInstance(ProductController::class);

        $reflection = new \ReflectionClass(ProductController::class);
        $method = $reflection->getMethod('getErrorWithHighestSeverity');
        $error = $method->invoke($productController, $errors);

        self::assertSame(
            $expectedSeverity,
            $error->getSeverity()
        );

        self::assertSame(
            $expectedMessage,
            $error->getTitle()
        );

        self::assertSame(
            $expectedMessage,
            $error->getMessage()
        );
    }

    public static function getLastHighestSeverityDataProvider(): Traversable
    {
        yield [
            'errors' => [
                new FlashMessage(
                    'WARNING 1',
                    'WARNING 1',
                    ContextualFeedbackSeverity::WARNING
                ),
                new FlashMessage(
                    'OK',
                    'OK',
                    ContextualFeedbackSeverity::OK
                ),
                new FlashMessage(
                    'WARNING 2',
                    'WARNING 2',
                    ContextualFeedbackSeverity::WARNING
                ),
            ],
            'expectedSeverity' => ContextualFeedbackSeverity::WARNING,
            'expectedMessage' => 'WARNING 2',
        ];
        yield [
            'errors' => [
                new FlashMessage(
                    'WARNING 2',
                    'WARNING 2',
                    ContextualFeedbackSeverity::WARNING
                ),
                new FlashMessage(
                    'WARNING 1',
                    'WARNING 1',
                    ContextualFeedbackSeverity::WARNING
                ),
            ],
            'expectedSeverity' => ContextualFeedbackSeverity::WARNING,
            'expectedMessage' => 'WARNING 1',
        ];
        yield [
            'errors' => [
                new FlashMessage(
                    'WARNING 1',
                    'WARNING 1',
                    ContextualFeedbackSeverity::WARNING
                ),
                new FlashMessage(
                    'ERROR 1',
                    'ERROR 1',
                    ContextualFeedbackSeverity::ERROR
                ),
                new FlashMessage(
                    'ERROR 2',
                    'ERROR 2',
                    ContextualFeedbackSeverity::ERROR
                ),
                new FlashMessage(
                    'WARNING 2',
                    'WARNING 2',
                    ContextualFeedbackSeverity::WARNING
                ),
            ],
            'expectedSeverity' => ContextualFeedbackSeverity::ERROR,
            'expectedMessage' => 'ERROR 2',
        ];
    }

}
