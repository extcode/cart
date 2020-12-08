<?php

namespace Extcode\Cart\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Hooks\MailAttachmentHookInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class MailHandler implements SingletonInterface
{
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
    protected $buyerEmailBcc = '';

    /**
     * @var string
     */
    protected $sellerEmailFrom = '';

    /**
     * @var string
     */
    protected $sellerEmailTo = '';

    /**
     * @var string
     */
    protected $sellerEmailBcc = '';

    /**
     * MailHandler constructor
     */
    public function __construct()
    {
        $this->logManager = GeneralUtility::makeInstance(
            LogManager::class
        );

        $this->configurationManager = GeneralUtility::makeInstance(
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

            if (!empty($this->pluginSettings['settings']['buyer'])
                && !empty($this->pluginSettings['settings']['buyer']['emailBccAddress'])
            ) {
                $this->setBuyerEmailBcc($this->pluginSettings['settings']['buyer']['emailBccAddress']);
            } elseif (!empty($this->pluginSettings['mail'])
                && !empty($this->pluginSettings['mail']['buyer'])
                && !empty($this->pluginSettings['mail']['buyer']['bccAddress'])
            ) {
                $this->setBuyerEmailBcc($this->pluginSettings['mail']['buyer']['bccAddress']);
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

            if (!empty($this->pluginSettings['settings']['seller'])
                && !empty($this->pluginSettings['settings']['seller']['emailBccAddress'])
            ) {
                $this->setSellerEmailBcc($this->pluginSettings['settings']['seller']['emailBccAddress']);
            } elseif (!empty($this->pluginSettings['mail'])
                && !empty($this->pluginSettings['mail']['seller'])
                && !empty($this->pluginSettings['mail']['seller']['bccAddress'])
            ) {
                $this->setSellerEmailBcc($this->pluginSettings['mail']['seller']['bccAddress']);
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
    public function setBuyerEmailFrom(string $email): void
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
     * @param string $buyerEmailBcc
     */
    public function setBuyerEmailBcc(string $buyerEmailBcc): void
    {
        $this->buyerEmailBcc = $buyerEmailBcc;
    }

    /**
     * @return string
     */
    public function getBuyerEmailBcc(): string
    {
        return $this->buyerEmailBcc;
    }

    /**
     * @param string $email
     */
    public function setSellerEmailFrom(string $email): void
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
    public function setSellerEmailTo(string $email): void
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
     * @param string $sellerEmailBcc
     */
    public function setSellerEmailBcc(string $sellerEmailBcc): void
    {
        $this->sellerEmailBcc = $sellerEmailBcc;
    }

    /**
     * @return string
     */
    public function getSellerEmailBcc(): string
    {
        return $this->sellerEmailBcc;
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

        $email = GeneralUtility::makeInstance(FluidEmail::class)
            ->to($orderItem->getBillingAddress()->getEmail())
            ->from($this->buyerEmailFrom)
            ->setTemplate('Mail/' . ucfirst($status) . '/Buyer')
            ->format(\TYPO3\CMS\Core\Mail\FluidEmail::FORMAT_HTML)
            ->assign('settings', $this->pluginSettings['settings'])
            ->assign('cart', $this->cart)
            ->assign('orderItem', $orderItem);

        if ($this->buyerEmailBcc) {
            $email->bcc(explode(',', $this->buyerEmailBcc));
        }

        /*
           if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['MailAttachmentsHook']) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['MailAttachmentsHook'] as $className) {
                    $_procObj = GeneralUtility::makeInstance($className);
                    if (!$_procObj instanceof MailAttachmentHookInterface) {
                        throw new \UnexpectedValueException($className . ' must implement interface ' . MailAttachmentHookInterface::class, 123);
                    }

                    $mail = $_procObj->getMailAttachments($mail, $orderItem, $to);
                }
            }
         */

        if ($GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface) {
            $email->setRequest($GLOBALS['TYPO3_REQUEST']);
        }

        GeneralUtility::makeInstance(Mailer::class)->send($email);
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

        $email = GeneralUtility::makeInstance(FluidEmail::class)
            ->to($orderItem->getBillingAddress()->getEmail())
            ->from($this->buyerEmailFrom)
            ->setTemplate('Mail/' . ucfirst($status) . '/Seller')
            ->format(\TYPO3\CMS\Core\Mail\FluidEmail::FORMAT_HTML)
            ->assign('settings', $this->pluginSettings['settings'])
            ->assign('cart', $this->cart)
            ->assign('orderItem', $orderItem);

        if ($orderItem->getBillingAddress()->getEmail()) {
            $email->replyTo($orderItem->getBillingAddress()->getEmail());
        }
        if ($this->sellerEmailBcc) {
            $bcc = explode(',', $this->sellerEmailBcc);
            $email->bcc(...$bcc);
        }

        /*
            if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['getMailAttachmentsHook']) {
                foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['getMailAttachmentsHook'] as $_classRef) {
                    $_procObj = &GeneralUtility::makeInstance($_classRef);

                    $mail = $_procObj->getMailAttachments($mail, $orderItem, $to);
                }
            }
        */

        if ($GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface) {
            $email->setRequest($GLOBALS['TYPO3_REQUEST']);
        }

        GeneralUtility::makeInstance(Mailer::class)->send($email);
    }
}
