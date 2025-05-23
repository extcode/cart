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
use Extcode\Cart\EventListener\Mail\AttachmentFromOrderItem;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class AttachmentFromOrderItemTest extends FunctionalTestCase
{
    use TestingFramework;

    public function setUp(): void
    {
        $this->testExtensionsToLoad[] = 'extcode/cart';
        $this->testExtensionsToLoad[] = 'typo3conf/ext/cart/Tests/Fixtures/cart_example';

        parent::setUp();

        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);

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

        self::assertContains(AttachmentFromOrderItem::class, $listenersClassNames);
    }
}
