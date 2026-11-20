<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Mail\AttachmentEvent;
use Extcode\Cart\Mail\PluginConfigurationProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AttachmentFromTypoScript
{
    public function __construct(
        private readonly PluginConfigurationProvider $pluginConfigurationProvider
    ) {
    }

    public function __invoke(AttachmentEvent $event): void
    {
        $attachments = $this->pluginConfigurationProvider->getMailConfiguration($event->getType(), 'attachments');
        foreach ($attachments as $attachment) {
            $attachmentFile = GeneralUtility::getFileAbsFileName($attachment);
            if (file_exists($attachmentFile)) {
                $event->addAttachment($attachmentFile);
            }
        }
    }
}
