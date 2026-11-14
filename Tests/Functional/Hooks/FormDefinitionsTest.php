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
 * like this test does. This only works if FormDefinitions is registered as
 * public service with its dependencies.
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
        $this->setUpBackendUser(1);

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://localhost/typo3/'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        parent::tearDown();
    }

    #[Test]
    public function itemsProcFuncCanBeCreatedAndListsTheForms(): void
    {
        $config = [
            'field' => 'form_definition',
            'config' => [
                'itemsProcFuncConfig' => [
                    'prototypeName' => 'cart',
                ],
            ],
            'items' => [],
        ];

        GeneralUtility::makeInstance(FormDefinitions::class)->getItems($config);

        self::assertSame([], $config['items']);
    }
}
