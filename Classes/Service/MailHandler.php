<?php

namespace Extcode\Cart\Service;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\SingletonInterface;

/**
 * MailHandler
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class MailHandler implements SingletonInterface
{
    /**
     * Extension Name
     *
     * @var string
     */
    protected $extensionName = 'Cart';

    /**
     * Plugin Name
     *
     * @var string
     */
    protected $pluginName = 'Cart';

    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * Log Manager
     *
     * @var \TYPO3\CMS\Core\Log\LogManager
     */
    protected $logManager;

    /**
     * Configuration Manager
     *
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     * @inject
     */
    protected $configurationManager;

    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;

    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    /**
     * Buyer Email From Address
     *
     * @var string
     */
    protected $buyerEmailFrom;

    /**
     * Seller Email From Address
     *
     * @var string
     */
    protected $sellerEmailFrom;

    /**
     * Seller Email To Address
     *
     * @var string
     */
    protected $sellerEmailTo;

    /**
     * MailHandler constructor
     */
    public function __construct()
    {
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\CMS\Extbase\Object\ObjectManager'
        );

        $this->logManager = $this->objectManager->get(
            \TYPO3\CMS\Core\Log\LogManager::class
        );

        $this->configurationManager = $this->objectManager->get(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class
        );

        $pluginSettings =
            $this->configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                $this->extensionName
            );

        $this->setPluginSettings($pluginSettings);
    }

    /**
     * Sets Plugin Settings
     *
     * @param array $pluginSettings
     *
     * @return void
     */
    public function setPluginSettings($pluginSettings)
    {
        $this->pluginSettings = $pluginSettings;

        if (!empty($this->pluginSettings['settings'])) {
            if (!empty($this->pluginSettings['settings']['buyer'])
                && !empty($this->pluginSettings['settings']['buyer']['emailFromAddress'])
            ) {
                $this->setBuyerEmailFrom($this->pluginSettings['settings']['buyer']['emailFromAddress']);
            } elseif (!empty($this->pluginSettings['mail'])
                && !empty($this->pluginSettings['mail']['buyer'])
                && !empty($this->pluginSettings['mail']['buyer']['fromAddress'])
            ) {
                $this->setBuyerEmailFrom($this->pluginSettings['mail']['buyer']['fromAddress']);
            }

            if (!empty($this->pluginSettings['settings']['seller'])
                && !empty($this->pluginSettings['settings']['seller']['emailFromAddress'])
            ) {
                $this->setSellerEmailFrom($this->pluginSettings['settings']['seller']['emailFromAddress']);
            } elseif (!empty($this->pluginSettings['mail'])
                && !empty($this->pluginSettings['mail']['seller'])
                && !empty($this->pluginSettings['mail']['seller']['fromAddress'])
            ) {
                $this->setSellerEmailFrom($this->pluginSettings['mail']['seller']['fromAddress']);
            }

            if (!empty($this->pluginSettings['settings']['seller'])
                && !empty($this->pluginSettings['settings']['seller']['emailToAddress'])
            ) {
                $this->setSellerEmailTo($this->pluginSettings['settings']['seller']['emailToAddress']);
            } elseif (!empty($this->pluginSettings['mail'])
                && !empty($this->pluginSettings['mail']['seller'])
                && !empty($this->pluginSettings['mail']['seller']['toAddress'])
            ) {
                $this->setSellerEmailTo($this->pluginSettings['mail']['seller']['toAddress']);
            }
        }
    }

    /**
     * Sets Cart
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     *
     * @return void
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }

    /**
     * @param string $email
     *
     * @return void
     */
    public function setBuyerEmailFrom($email)
    {
        $this->buyerEmailFrom = $email;
    }

    /**
     * @return string
     */
    public function getBuyerEmailFrom()
    {
        return $this->buyerEmailFrom;
    }

    /**
     * @param string $email
     *
     * @return void
     */
    public function setSellerEmailFrom($email)
    {
        $this->sellerEmailFrom = $email;
    }

    /**
     * @return string
     */
    public function getSellerEmailFrom()
    {
        return $this->sellerEmailFrom;
    }

    /**
     * @param string $email
     *
     * @return void
     */
    public function setSellerEmailTo($email)
    {
        $this->sellerEmailTo = $email;
    }

    /**
     * @return string
     */
    public function getSellerEmailTo()
    {
        return $this->sellerEmailTo;
    }

    /**
     * Send a Mail to Buyer
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     *
     * @return void
     */
    public function sendBuyerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        if (empty($this->buyerEmailFrom) || empty($billingAddress->getEmail())) {
            return;
        }

        $status = $orderItem->getPayment()->getStatus();
        $to = 'buyer';

        $mailBody = $this->renderMailStandaloneView($status, $to, $orderItem);
        $mailSubject = $this->renderMailStandaloneView($status, $to . 'Subject', $orderItem);

        if (!empty($mailBody) && !empty($mailSubject)) {
            $mail = $this->objectManager->get(
                \TYPO3\CMS\Core\Mail\MailMessage::class
            );
            $mail->setFrom($this->buyerEmailFrom);
            $mail->setTo($billingAddress->getEmail());
            $mail->setSubject($mailSubject);
            $mail->setBody($mailBody, 'text/html', 'utf-8');

            // get and add attachments
            $attachments = $this->getAttachments($orderItem, $to);
            foreach ($attachments as $attachment) {
                $attachmentFile = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($attachment);
                if (file_exists($attachmentFile)) {
                    $mail->attach(\Swift_Attachment::fromPath($attachmentFile));
                }
            }

            $mail->send();
        }
    }

    /**
     * Send a Mail to Seller
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     *
     * @return void
     */
    public function sendSellerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        if (empty($this->sellerEmailFrom) || empty($this->sellerEmailTo)) {
            return;
        }

        $status = $orderItem->getPayment()->getStatus();
        $to = 'seller';

        $mailBody = $this->renderMailStandaloneView($status, $to, $orderItem);
        $mailSubject = $this->renderMailStandaloneView($status, $to . 'Subject', $orderItem);

        if (!empty($mailBody) && !empty($mailSubject)) {
            $mail = $this->objectManager->get(
                \TYPO3\CMS\Core\Mail\MailMessage::class
            );
            $mail->setFrom($this->sellerEmailFrom);
            $mail->setTo(explode(',', $this->sellerEmailTo));
            $mail->setReplyTo($billingAddress->getEmail());
            $mail->setSubject($mailSubject);
            $mail->setBody($mailBody, 'text/html', 'utf-8');

            // get and add attachments
            $attachments = $this->getAttachments($orderItem, $to);
            foreach ($attachments as $attachment) {
                $attachmentFile = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($attachment);
                if (file_exists($attachmentFile)) {
                    $mail->attach(\Swift_Attachment::fromPath($attachmentFile));
                }
            }

            $mail->send();
        }
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param string $to
     *
     * @return array
     */
    protected function getAttachments(\Extcode\Cart\Domain\Model\Order\Item $orderItem, $to)
    {
        $attachments = [];

        if ($this->pluginSettings['mail'] && $this->pluginSettings['mail'][$to]) {
            if ($this->pluginSettings['mail'][$to]['attachments']) {
                $attachments = $this->pluginSettings['mail'][$to]['attachments'];
            }
            if ($this->pluginSettings['mail'][$to]['attachDocuments']) {
                foreach ($this->pluginSettings['mail'][$to]['attachDocuments'] as $pdfType => $pdfData) {
                    $getter = 'get' . ucfirst($pdfType) . 'Pdfs';
                    $pdfs = $orderItem->$getter();
                    if ($pdfs && ($pdfs instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage)) {
                        $pdfs = end($pdfs->toArray());
                        if ($pdfs) {
                            $lastOriginalPdf = $pdfs->getOriginalResource();
                            $lastOriginalPdfPath = PATH_site . $lastOriginalPdf->getPublicUrl();
                            if (is_file($lastOriginalPdfPath)) {
                                $attachments[] = $lastOriginalPdfPath;

                            }
                        }
                    }
                }
            }
        }

        return $attachments;
    }

    /**
     * Render OrderItem Mail Content
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param string $mailTemplateFolder
     * @param string $mailTo
     *
     * @return array
     */
    public function renderOrderItemMailContent(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        $mailTemplateFolder,
        $mailTo
    ) {
        $mailSubject = $this->renderMailStandaloneView($mailTemplateFolder, $mailTo . 'Subject', $orderItem);
        $mailBody = $this->renderMailStandaloneView($mailTemplateFolder, $mailTo, $orderItem);

        return [$mailSubject, $mailBody];
    }

    /**
     * Returns the Mail Body
     *
     * @param string $status
     * @param string $to
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     *
     * @return string
     */
    protected function renderMailStandaloneView(
        $status,
        $to,
        \Extcode\Cart\Domain\Model\Order\Item $orderItem
    ) {
        $view = $this->getMailStandaloneView('/Mail/' . ucfirst($status) . '/', ucfirst($to), 'html');

        if ($view->getTemplatePathAndFilename()) {
            $view->assign('settings', $this->pluginSettings['settings']);

            $view->assign('cart', $this->cart);
            $view->assign('orderItem', $orderItem);

            // ToDo: Remove assign $billingAddress and $shippingAddress to view. Both can be used in view through $orderItem.

            if ($orderItem->getBillingAddress()) {
                $view->assign('billingAddress', $orderItem->getBillingAddress());
            }

            if ($orderItem->getShippingAddress()) {
                $view->assign('shippingAddress', $orderItem->getShippingAddress());
            }

            return $view->render();
        }

        return '';
    }

    /**
     * This creates another stand-alone instance of the Fluid StandaloneView
     * to render an e-mail template
     *
     * @param string $templateSubPath
     * @param string $templateFileName
     * @param string $format
     *
     * @return \TYPO3\CMS\Fluid\View\StandaloneView Fluid instance
     */
    protected function getMailStandaloneView(
        $templateSubPath = '/Mail/',
        $templateFileName = 'Default',
        $format = 'html'
    ) {
        $templateSubPathAndFileName = $templateSubPath . $templateFileName . '.' . $format;

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
        $view = $this->objectManager->get(
            \TYPO3\CMS\Fluid\View\StandaloneView::class
        );
        $view->setFormat($format);

        if ($this->pluginSettings['view']) {
            $view->setLayoutRootPaths($this->resolveRootPaths('layoutRootPaths'));
            $view->setPartialRootPaths($this->resolveRootPaths('partialRootPaths'));

            if ($this->pluginSettings['view']['templateRootPaths']) {
                foreach ($this->pluginSettings['view']['templateRootPaths'] as $pathNameKey => $pathNameValue) {
                    $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(
                        $pathNameValue
                    );

                    $completePath = $templateRootPath . $templateSubPathAndFileName;
                    if (file_exists($completePath)) {
                        $view->setTemplatePathAndFilename($completePath);
                    }
                }
            }
        }

        if (!$view->getTemplatePathAndFilename()) {
            $logger = $this->logManager->getLogger(__CLASS__);
            $logger->error(
                'Cannot find Template for MailHandler',
                [
                    'templateRootPaths' => $this->pluginSettings['view']['templateRootPaths'],
                    'templatePathAndFileName' => $templateSubPathAndFileName,
                ]
            );
        }

        // set controller extension name for translation
        $view->getRequest()->setControllerExtensionName('Cart');

        return $view;
    }

    /**
     * Returns the Partial Root Path
     *
     * For TYPO3 Version 6.2 it resolves the absolute file names
     *
     * @var string $type
     * @return array
     *
     * @deprecated will be removed with support for TYPO3 6.2
     */
    protected function resolveRootPaths($type)
    {
        $rootPaths = [];

        if ($this->pluginSettings['view'][$type]) {
            $rootPaths = $this->pluginSettings['view'][$type];

            if (\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('6.2')) {
                foreach ($rootPaths as $rootPathsKey => $rootPathsValue) {
                    $rootPaths[$rootPathsKey] = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(
                        $rootPathsValue
                    );
                }
            }
        }

        return $rootPaths;
    }
}
