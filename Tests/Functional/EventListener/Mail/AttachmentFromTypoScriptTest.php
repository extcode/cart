<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\EventListener\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use Extcode\Cart\Event\Mail\AttachmentEvent;
use Extcode\Cart\EventListener\Mail\AttachmentFromTypoScript;
use Extcode\Cart\Mail\PluginConfigurationProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AttachmentFromTypoScriptTest extends FunctionalTestCase
{
    use TestingFramework;

    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'extcode/cart',
            'typo3conf/ext/cart/Tests/Fixtures/cart_example',
        ];

        $this->coreExtensionsToLoad = [
            'typo3/cms-beuser',
            'typo3/cms-core',
        ];

        parent::setUp();

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
        ;

        $this->importPHPDataSet(__DIR__ . '/../../../Fixtures/BaseDatabase.php');
    }

    #[Test]
    public function asCommandRegisteredToCommandRegistry(): void
    {
        $commandRegistry = $this->get(ListenerProvider::class);

        $attachmentEvent = new AttachmentEvent('buyer');

        $listeners = $commandRegistry->getListenersForEvent($attachmentEvent);
        $listenersClassNames = [];
        foreach ($listeners as $listener) {
            $listenersClassNames[] = $listener::class;
        }

        self::assertContains(AttachmentFromTypoScript::class, $listenersClassNames);
    }

    #[Test]
    public function withoutRequestNoFileIsAdded(): void
    {
        // e.g. on the command line, there is no TypoScript
        unset($GLOBALS['TYPO3_REQUEST']);
        $attachmentEvent = new AttachmentEvent('buyer');

        $attachmentFromTypoScript = new AttachmentFromTypoScript(
            new PluginConfigurationProvider(GeneralUtility::makeInstance(ConfigurationManager::class), new TypoScriptService())
        );
        $attachmentFromTypoScript->__invoke($attachmentEvent);

        self::assertSame([], $attachmentEvent->getAttachments());
    }

    #[Test]
    public function filesFromTypoScriptAddedToAttachmentList(): void
    {
        $attachmentEvent = new AttachmentEvent('buyer');

        // plugin.tx_cart of the page in the backend, the request of setUp() is a backend request
        $configurationManager = self::createStub(ConfigurationManagerInterface::class);
        $configurationManager->method('getConfiguration')->willReturn([
            'plugin.' => [
                'tx_cart.' => [
                    'mail.' => [
                        'buyer.' => [
                            'attachments.' => [
                                '1' => 'EXT:cart_example/Resources/Public/Files/Extension.pdf',
                                '2' => 'EXT:cart_example/Resources/Public/Files/NotExisting.pdf',
                                '3' => 'EXT:cart_example/Resources/Public/Icons/Extension.svg',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        $attachmentFromTypoScriot = new AttachmentFromTypoScript(
            new PluginConfigurationProvider($configurationManager, new TypoScriptService())
        );

        $attachmentFromTypoScriot->__invoke($attachmentEvent);

        $attachments = $attachmentEvent->getAttachments();

        self::assertCount(2, $attachments);
        self::assertContains(GeneralUtility::getFileAbsFileName('EXT:cart_example/Resources/Public/Files/Extension.pdf'), $attachments);
        self::assertContains(GeneralUtility::getFileAbsFileName('EXT:cart_example/Resources/Public/Icons/Extension.svg'), $attachments);
    }
}
