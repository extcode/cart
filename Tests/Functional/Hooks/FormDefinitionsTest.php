<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use Extcode\Cart\Hooks\FormDefinitions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * The FormEngine creates the itemsProcFunc with GeneralUtility::makeInstance(),
 * like this test does. The forms are stored in the database storage of
 * EXT:form, which lists every form with the prototype "standard".
 */
#[CoversClass(FormDefinitions::class)]
final class FormDefinitionsTest extends FunctionalTestCase
{
    use TestingFramework;

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    protected array $coreExtensionsToLoad = [
        'typo3/cms-form',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/BackendUser.php');
        $this->importPHPDataSet(__DIR__ . '/../Fixtures/FormDefinitions.php');
        $this->setUpBackendUser(1);

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://localhost/typo3/'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
        ;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        parent::tearDown();
    }

    #[Test]
    public function addsTheFormsOfTheDatabaseStorageWithThePrototypeCart(): void
    {
        self::assertSame(
            [
                ['label' => 'Form 2', 'value' => '2'],
                ['label' => 'Form 5', 'value' => '5'],
            ],
            self::getItems()
        );
    }

    #[Test]
    public function addsTheFormsOfTheConfiguredPrototype(): void
    {
        self::assertSame(
            [
                ['label' => 'Form 3', 'value' => '3'],
            ],
            self::getItems('cart-events')
        );
    }

    /**
     * @return array<mixed>
     */
    private static function getItems(?string $prototypeName = null): array
    {
        $config = [
            'field' => 'form_definition',
            'config' => [
                'itemsProcFuncConfig' => $prototypeName === null ? [] : ['prototypeName' => $prototypeName],
            ],
            'items' => [],
        ];

        GeneralUtility::makeInstance(FormDefinitions::class)->getItems($config);
        self::assertIsArray($config['items']);

        return $config['items'];
    }
}
