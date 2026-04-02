<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Command;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractCommandTestCase extends FunctionalTestCase
{
    use TestingFramework;

    private const FORM_PROTECTION_SESSION_TOKEN = 'testtoken';

    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'extcode/cart',
        ];

        $this->coreExtensionsToLoad = [
            'typo3/cms-beuser',
            'typo3/cms-core',
        ];

        $this->pathsToLinkInTestInstance['typo3conf/ext/cart/Tests/Functional/Fixtures/Import/Sites/'] = 'typo3conf/sites';

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/BackendUser.php');

        $this->setUpBackendUser(1)
            ->getSession()
            ->set('formProtectionSessionToken', self::FORM_PROTECTION_SESSION_TOKEN);

        $GLOBALS['LANG'] = $this->get(LanguageServiceFactory::class)->create('en');
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['LANG']
        );

        parent::tearDown();
    }
}
