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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

class AttachmentFromTypoScript
{
    private array $settings;

    public function __construct(
        private ConfigurationManager $configurationManager
    ) {
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
    }

    public function __invoke(AttachmentEvent $event): void
    {
        $type = $event->getType();
        if (!isset($this->settings['mail'][$type]['attachments'])) {
            return;
        }

        $attachments = $this->settings['mail'][$type]['attachments'];

        foreach ($attachments as $attachment) {
            $attachmentFile = GeneralUtility::getFileAbsFileName($attachment);
            if (file_exists($attachmentFile)) {
                $event->addAttachment($attachmentFile);
            }
        }
    }
}
