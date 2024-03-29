<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Mail\AttachementEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class AttachmentFromTypoScript
{
    private array $pluginSettings;

    public function __construct(
        private ConfigurationManager $configurationManager
    ) {
        $this->pluginSettings = $this->configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
    }

    public function __invoke(AttachementEvent $event): void
    {
        $type = $event->getType();
        if (!isset($this->pluginSettings['mail'][$type]['attachments'])) {
            return;
        }

        $attachments = $this->pluginSettings['mail'][$type]['attachments'];

        foreach ($attachments as $attachment) {
            $attachmentFile = GeneralUtility::getFileAbsFileName($attachment);
            if (file_exists($attachmentFile)) {
                $event->addAttachment($attachmentFile);
            }
        }
    }
}
