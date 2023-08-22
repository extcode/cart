<?php

declare(strict_types=1);

namespace Extcode\Cart\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class MailAttachmentHook implements MailAttachmentHookInterface
{
    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $pluginSettings = [];

    /**
     * MailHandler constructor
     */
    public function __construct()
    {
        $this->configurationManager = GeneralUtility::makeInstance(
            ConfigurationManager::class
        );

        $this->pluginSettings = $this->configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
        // DebuggerUtility::var_dump($this->pluginSettings);
    }

    /**
     * @param FluidEmail $mailMessage
     * @param Item $item
     * @param string $type = ['buyer' | 'seller']
     *
     * @return FluidEmail
     */
    public function getMailAttachments(FluidEmail $mailMessage, Item $item, string $type): FluidEmail
    {
        // if ($this->pluginSettings['mail'] && $this->pluginSettings['mail'][$type]) {
        //     if ($this->pluginSettings['mail'][$type]['attachments']) {
        //         $attachments = $this->pluginSettings['mail'][$type]['attachments'];

        //         foreach ($attachments as $attachment) {
        //             $attachmentFile = GeneralUtility::getFileAbsFileName($attachment);
        //             if (file_exists($attachmentFile)) {
        //                 $mailMessage->attachFromPath($attachmentFile);
        //             }
        //         }
        //     }

        //     if ($this->pluginSettings['mail'][$type]['attachDocuments']) {
        //         foreach ($this->pluginSettings['mail'][$type]['attachDocuments'] as $pdfType => $pdfData) {
        //             $getter = 'get' . ucfirst($pdfType) . 'Pdfs';
        //             $pdfs = $item->$getter();
        //             if ($pdfs && ($pdfs instanceof ObjectStorage)) {
        //                 $pdfs = end($pdfs->toArray());
        //                 if ($pdfs) {
        //                     $lastOriginalPdf = $pdfs->getOriginalResource();
        //                     $lastOriginalPdfPath = $lastOriginalPdf->getForLocalProcessing(false);
        //                     if (is_file($lastOriginalPdfPath)) {
        //                         $mailMessage->attachFromPath($lastOriginalPdfPath);
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        return $mailMessage;
    }
}
