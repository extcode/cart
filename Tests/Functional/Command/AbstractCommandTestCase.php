<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Command;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractCommandTestCase extends FunctionalTestCase
{
    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'extcode/cart',
        ];

        $this->coreExtensionsToLoad = [
            'typo3/cms-beuser',
        ];;

        $this->pathsToLinkInTestInstance['typo3conf/ext/cart/Tests/Functional/Fixtures/Import/Sites/'] = 'typo3conf/sites';

        parent::setUp();

        $backendUser = self::createStub(BackendUserAuthentication::class);
        $backendUser->method('isAdmin')->willReturn(true);
        $backendUser->method('recordEditAccessInternals')->willReturn(true);
        $backendUser->workspace = 0;
        $backendUser->user = [
            'uid' => 1,
            'admin' => true,
        ];
        $GLOBALS['BE_USER'] = $backendUser;
        $GLOBALS['LANG'] = $this->get(LanguageServiceFactory::class)->create('en');
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['BE_USER'],
            $GLOBALS['LANG']
        );

        parent::tearDown();
    }
}
