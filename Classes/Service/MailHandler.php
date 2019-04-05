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
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Hooks\MailAttachmentHookInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * MailHandler
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class MailHandler implements SingletonInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var LogManager
     */
    protected $logManager;

    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $pluginSettings = [];

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var string
     */
    protected $buyerEmailFrom = '';

    /**
     * @var string
     */
    protected $sellerEmailFrom = '';

    /**
     * @var string
     */
    protected $sellerEmailTo = '';

    /**
     * MailHandler constructor
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(
            ObjectManager::class
        );

        $this->logManager = $this->objectManager->get(
            LogManager::class
        );

        $this->configurationManager = $this->objectManager->get(
            ConfigurationManager::class
        );

        $this->setPluginSettings();
    }

    /**
     * Sets Plugin Settings
     */
    public function setPluginSettings()
    {
        $this->pluginSettings =
            $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                'Cart'
            );

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
     * @param Cart $cart
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @param string $email
     */
    public function setBuyerEmailFrom(string $email)
    {
        $this->buyerEmailFrom = $email;
    }

    /**
     * @return string
     */
    public function getBuyerEmailFrom(): string
    {
        return $this->buyerEmailFrom;
    }

    /**
     * @param string $email
     */
    public function setSellerEmailFrom(string $email)
    {
        $this->sellerEmailFrom = $email;
    }

    /**
     * @return string
     */
    public function getSellerEmailFrom(): string
    {
        return $this->sellerEmailFrom;
    }

    /**
     * @param string $email
     */
    public function setSellerEmailTo(string $email)
    {
        $this->sellerEmailTo = $email;
    }

    /**
     * @return string
     */
    public function getSellerEmailTo(): string
    {
        return $this->sellerEmailTo;
    }

    /**
     * Send a Mail to Buyer
     *
     * @param Item $orderItem
     */
    public function sendBuyerMail(Item $orderItem)
    {
        if (empty($this->buyerEmailFrom) || empty($orderItem->getBillingAddress()->getEmail())) {
            return;
        }

        $status = $orderItem->getPayment()->getStatus();
        $to = 'buyer';

        $mailBody = $this->renderMailStandaloneView($status, $to, $orderItem);
        $mailSubject = $this->renderMailStandaloneView($status, $to . 'Subject', $orderItem);

        if (!empty($mailBody) && !empty($mailSubject)) {
            $mail = $this->objectManager->get(MailMessage::class);
            $mail->setFrom($this->buyerEmailFrom);
            $mail->setTo($orderItem->getBillingAddress()->getEmail());
            $mail->setSubject($mailSubject);
            $mail->setBody($mailBody, 'text/html', 'utf-8');

            if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['MailAttachmentsHook']) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['MailAttachmentsHook'] as $className) {
                    $_procObj = GeneralUtility::makeInstance($className);
                    if (!$_procObj instanceof MailAttachmentHookInterface) {
                        throw new \UnexpectedValueException($className . ' must implement interface ' . MailAttachmentHookInterface::class, 123);
                    }

                    $mail = $_procObj->getMailAttachments($mail, $orderItem, $to);
                }
            }

            $mail->send();
        }
    }

    /**
     * Send a Mail to Seller
     *
     * @param Item $orderItem
     */
    public function sendSellerMail(Item $orderItem)
    {
        if (empty($this->sellerEmailFrom) || empty($this->sellerEmailTo)) {
            return;
        }

        $status = $orderItem->getPayment()->getStatus();
        $to = 'seller';

        $mailBody = $this->renderMailStandaloneView($status, $to, $orderItem);
        $mailSubject = $this->renderMailStandaloneView($status, $to . 'Subject', $orderItem);

        if (!empty($mailBody) && !empty($mailSubject)) {
            $mail = $this->objectManager->get(MailMessage::class);
            $mail->setFrom($this->sellerEmailFrom);
            $mail->setTo(explode(',', $this->sellerEmailTo));
            $mail->setReplyTo($orderItem->getBillingAddress()->getEmail());
            $mail->setSubject($mailSubject);
            $mail->setBody($mailBody, 'text/html', 'utf-8');

            if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['getMailAttachmentsHook']) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['getMailAttachmentsHook'] as $_classRef) {
                    $_procObj = &GeneralUtility::makeInstance($_classRef);

                    $mail = $_procObj->getMailAttachments($mail, $orderItem, $to);
                }
            }

            $mail->send();
        }
    }

    /**
     * Render OrderItem Mail Content
     *
     * @param Item $orderItem
     * @param string $mailTemplateFolder
     * @param string $mailTo
     *
     * @return array
     */
    public function renderOrderItemMailContent(
        Item $orderItem,
        string $mailTemplateFolder,
        string $mailTo
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
     * @param Item $orderItem
     *
     * @return string
     */
    protected function renderMailStandaloneView(
        string $status,
        string $to,
        Item $orderItem
    ) {
        $view = $this->getMailStandaloneView('/Mail/' . ucfirst($status) . '/', ucfirst($to), 'html');

        if ($view->getTemplatePathAndFilename()) {
            $view->assign('settings', $this->pluginSettings['settings']);

            $view->assign('cart', $this->cart);
            $view->assign('orderItem', $orderItem);

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
     * @return StandaloneView
     */
    protected function getMailStandaloneView(
        string $templateSubPath = '/Mail/',
        string $templateFileName = 'Default',
        string $format = 'html'
    ) {
        $templateSubPathAndFileName = $templateSubPath . $templateFileName . '.' . $format;

        $view = $this->objectManager->get(StandaloneView::class);
        $view->setFormat($format);

        if ($this->pluginSettings['view']) {
            $view->setLayoutRootPaths($this->resolveRootPaths('layoutRootPaths'));
            $view->setPartialRootPaths($this->resolveRootPaths('partialRootPaths'));

            if ($this->pluginSettings['view']['templateRootPaths']) {
                foreach ($this->pluginSettings['view']['templateRootPaths'] as $pathNameKey => $pathNameValue) {
                    $templateRootPath = GeneralUtility::getFileAbsFileName(
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
     * @var string $type
     *
     * @return array
     */
    protected function resolveRootPaths($type)
    {
        if ($this->pluginSettings['view'][$type]) {
            return $this->pluginSettings['view'][$type];
        }

        return [];
    }
}
