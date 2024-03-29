<?php

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
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AttachmentFromTypoScriptTest extends FunctionalTestCase
{
    use TestingFramework;

    protected array $testExtensionsToLoad = [
        'extcode/cart',
        'typo3conf/ext/cart/Tests/Fixtures/cart_example',
    ];

    public function setUp(): void
    {
        parent::setUp();
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
            $listenersClassNames[] = get_class($listener);
        }

        self::assertContains(AttachmentFromTypoScript::class, $listenersClassNames);
    }

    #[Test]
    public function filesFromTypoScriptAddedToAttachmentList()
    {
        $attachmentEvent = new AttachmentEvent('buyer');

        $attachmentFromTypoScriot = new AttachmentFromTypoScript(GeneralUtility::makeInstance(ConfigurationManager::class));

        $pluginSettings = [
            'mail' => [
                'buyer' => [
                    'attachments' => [
                        '1' => 'EXT:cart_example/Resources/Public/Files/Extension.pdf',
                        '2' => 'EXT:cart_example/Resources/Public/Files/NotExisting.pdf',
                        '3' => 'EXT:cart_example/Resources/Public/Icons/Extension.svg',
                    ],
                ],
            ],
        ];

        $reflection = new \ReflectionClass($attachmentFromTypoScriot);
        $reflection_property = $reflection->getProperty('pluginSettings');
        $reflection_property->setValue($attachmentFromTypoScriot, $pluginSettings);

        $attachmentFromTypoScriot->__invoke($attachmentEvent);

        $attachments = $attachmentEvent->getAttachments();

        self::assertSame(2, count($attachments));
        self::assertContains(GeneralUtility::getFileAbsFileName('EXT:cart_example/Resources/Public/Files/Extension.pdf'), $attachments);
        self::assertContains(GeneralUtility::getFileAbsFileName('EXT:cart_example/Resources/Public/Icons/Extension.svg'), $attachments);
    }
}
