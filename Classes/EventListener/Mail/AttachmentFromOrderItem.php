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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class AttachmentFromOrderItem
{
    public function __construct(
        private readonly PluginConfigurationProvider $pluginConfigurationProvider
    ) {
    }

    public function __invoke(AttachmentEvent $event): void
    {
        $attachDocuments = $this->pluginConfigurationProvider->getMailConfiguration($event->getType(), 'attachDocuments');
        if ($attachDocuments === []) {
            return;
        }

        $orderItem = $event->getOrderItem();
        if (!isset($orderItem)) {
            return;
        }

        foreach ($attachDocuments as $pdfType => $pdfData) {
            if ($pdfData !== '1' && $pdfData !== 'true') {
                continue;
            }
            $getter = 'get' . ucfirst((string) $pdfType) . 'Pdfs';
            $pdfs = $orderItem->$getter();
            if ($pdfs && ($pdfs instanceof ObjectStorage)) {
                $documents = $pdfs->toArray();
                $pdfs = end($documents);
                if ($pdfs) {
                    $lastOriginalPdf = $pdfs->getOriginalResource();
                    $lastOriginalPdfPath = $lastOriginalPdf->getForLocalProcessing(false);
                    if (is_file($lastOriginalPdfPath)) {
                        $event->addAttachment($lastOriginalPdfPath);
                    }
                }
            }
        }
    }
}
