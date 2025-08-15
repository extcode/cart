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
use Extcode\Cart\Event\Mail\AttachmentEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class MailHandler implements SingletonInterface
{
    protected array $pluginSettings = [];

    protected ?Cart $cart = null;

    protected string $buyerEmailName = '';
    protected string $buyerEmailFrom = '';
    protected string $buyerEmailCc = '';
    protected string $buyerEmailBcc = '';
    protected string $buyerEmailReplyTo = '';
    protected string $sellerEmailName = '';
    protected string $sellerEmailFrom = '';
    protected string $sellerEmailTo = '';
    protected string $sellerEmailCc = '';
    protected string $sellerEmailBcc = '';

    /**
     * MailHandler constructor
     */
    public function __construct(
        private readonly ConfigurationManagerInterface $configurationManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly MailerInterface $mailer
    ) {
        $this->setPluginSettings();
    }

    public function setPluginSettings(): void
    {
        $this->pluginSettings
            = $this->configurationManager->getConfiguration(
                ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK,
                'Cart'
            );

        if (empty($this->pluginSettings['settings'])) {
            return;
        }

        // setBuyerEmailName
        if (!empty($this->pluginSettings['settings']['buyer'])
            && !empty($this->pluginSettings['settings']['buyer']['emailFromName'])
        ) {
            $this->setBuyerEmailName($this->pluginSettings['settings']['buyer']['emailFromName']);
        } elseif (!empty($this->pluginSettings['mail'])
            && !empty($this->pluginSettings['mail']['buyer'])
            && !empty($this->pluginSettings['mail']['buyer']['fromName'])
        ) {
            $this->setBuyerEmailName($this->pluginSettings['mail']['buyer']['fromName']);
        }

        // setBuyerEmailFrom
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

        // setBuyerEmailCc
        if (!empty($this->pluginSettings['settings']['buyer'])
            && !empty($this->pluginSettings['settings']['buyer']['emailCcAddress'])
        ) {
            $this->setBuyerEmailCc($this->pluginSettings['settings']['buyer']['emailCcAddress']);
        } elseif (!empty($this->pluginSettings['mail'])
            && !empty($this->pluginSettings['mail']['buyer'])
            && !empty($this->pluginSettings['mail']['buyer']['ccAddress'])
        ) {
            $this->setBuyerEmailCc($this->pluginSettings['mail']['buyer']['ccAddress']);
        }

        // setBuyerEmailBcc
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

        // setBuyerEmailReplyTo
        if (!empty($this->pluginSettings['settings']['buyer'])
            && !empty($this->pluginSettings['settings']['buyer']['emailReplyToAddress'])
        ) {
            $this->setBuyerEmailReplyTo($this->pluginSettings['settings']['buyer']['emailReplyToAddress']);
        } elseif (!empty($this->pluginSettings['mail'])
            && !empty($this->pluginSettings['mail']['buyer'])
            && !empty($this->pluginSettings['mail']['buyer']['replyToAddress'])
        ) {
            $this->setBuyerEmailReplyTo($this->pluginSettings['mail']['buyer']['replyToAddress']);
        }

        // setSellerEmailName
        if (!empty($this->pluginSettings['settings']['seller'])
            && !empty($this->pluginSettings['settings']['seller']['emailFromName'])
        ) {
            $this->setSellerEmailName($this->pluginSettings['settings']['seller']['emailFromName']);
        } elseif (!empty($this->pluginSettings['mail'])
            && !empty($this->pluginSettings['mail']['seller'])
            && !empty($this->pluginSettings['mail']['seller']['fromName'])
        ) {
            $this->setSellerEmailName($this->pluginSettings['mail']['seller']['fromName']);
        }

        // setSellerEmailFrom
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

        // setSellerEmailTo
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

        // setSellerEmailCc
        if (!empty($this->pluginSettings['settings']['seller'])
            && !empty($this->pluginSettings['settings']['seller']['emailCcAddress'])
        ) {
            $this->setSellerEmailCc($this->pluginSettings['settings']['seller']['emailCcAddress']);
        } elseif (!empty($this->pluginSettings['mail'])
            && !empty($this->pluginSettings['mail']['seller'])
            && !empty($this->pluginSettings['mail']['seller']['ccAddress'])
        ) {
            $this->setSellerEmailCc($this->pluginSettings['mail']['seller']['ccAddress']);
        }

        // setSellerEmailBcc
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

    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
    }

    public function setBuyerEmailName(string $name): void
    {
        $this->buyerEmailName = $name;
    }

    public function getBuyerEmailName(): string
    {
        return $this->buyerEmailName;
    }

    public function setBuyerEmailFrom(string $email): void
    {
        $this->buyerEmailFrom = $email;
    }

    public function getBuyerEmailFrom(): string
    {
        return $this->buyerEmailFrom;
    }

    public function setBuyerEmailCc(string $buyerEmailCc): void
    {
        $this->buyerEmailCc = $buyerEmailCc;
    }

    public function getBuyerEmailCc(): string
    {
        return $this->buyerEmailCc;
    }

    public function setBuyerEmailBcc(string $buyerEmailBcc): void
    {
        $this->buyerEmailBcc = $buyerEmailBcc;
    }

    public function getBuyerEmailBcc(): string
    {
        return $this->buyerEmailBcc;
    }

    public function setBuyerEmailReplyTo(string $buyerEmailReplyTo): void
    {
        $this->buyerEmailReplyTo = $buyerEmailReplyTo;
    }

    public function getBuyerEmailReplyTo(): string
    {
        return $this->buyerEmailReplyTo;
    }

    public function setSellerEmailName(string $name): void
    {
        $this->sellerEmailName = $name;
    }

    public function getSellerEmailName(): string
    {
        return $this->sellerEmailName;
    }

    public function setSellerEmailFrom(string $email): void
    {
        $this->sellerEmailFrom = $email;
    }

    public function getSellerEmailFrom(): string
    {
        return $this->sellerEmailFrom;
    }

    public function setSellerEmailTo(string $email): void
    {
        $this->sellerEmailTo = $email;
    }

    public function getSellerEmailTo(): string
    {
        return $this->sellerEmailTo;
    }

    public function setSellerEmailCc(string $sellerEmailCc): void
    {
        $this->sellerEmailCc = $sellerEmailCc;
    }

    public function getSellerEmailCc(): string
    {
        return $this->sellerEmailCc;
    }

    public function setSellerEmailBcc(string $sellerEmailBcc): void
    {
        $this->sellerEmailBcc = $sellerEmailBcc;
    }

    public function getSellerEmailBcc(): string
    {
        return $this->sellerEmailBcc;
    }

    /**
     * Send a Mail to Buyer
     */
    public function sendBuyerMail(Item $orderItem): void
    {
        if (empty($this->getBuyerEmailFrom()) || empty($orderItem->getBillingAddress()->getEmail())) {
            return;
        }

        $status = $orderItem->getPayment()->getStatus();

        $fromAddress = new Address($this->getBuyerEmailFrom(), $this->getBuyerEmailName());

        $email = new FluidEmail();
        $email
            ->to($orderItem->getBillingAddress()->getEmail())
            ->from($fromAddress)
            ->setTemplate('Mail/' . ucfirst($status) . '/Buyer')
            ->format(FluidEmail::FORMAT_HTML)
            ->assign('settings', $this->pluginSettings['settings'])
            ->assign('cart', $this->cart)
            ->assign('orderItem', $orderItem);

        if ($this->getBuyerEmailCc()) {
            $cc = explode(',', $this->getBuyerEmailCc());
            $email->cc(...$cc);
        }
        if ($this->getBuyerEmailBcc()) {
            $bcc = explode(',', $this->getBuyerEmailBcc());
            $email->bcc(...$bcc);
        }
        if ($this->getbuyerEmailReplyTo()) {
            $email->replyTo($this->getbuyerEmailReplyTo());
        }

        $this->addAttachments('buyer', $orderItem, $email);

        if ($GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface) {
            $email->setRequest($GLOBALS['TYPO3_REQUEST']);
        }

        $this->mailer->send($email);
    }

    /**
     * Send a Mail to Seller
     */
    public function sendSellerMail(Item $orderItem): void
    {
        $sellerEmailTo = $this->getSellerEmailTo();
        if (empty($this->getSellerEmailFrom()) || empty($sellerEmailTo)) {
            return;
        }

        $status = $orderItem->getPayment()->getStatus();

        $fromAddress = new Address($this->getSellerEmailFrom(), $this->getSellerEmailName());

        $to = explode(',', $sellerEmailTo);
        $email = new FluidEmail();
        $email
            ->to(...$to)
            ->from($fromAddress)
            ->setTemplate('Mail/' . ucfirst($status) . '/Seller')
            ->format(FluidEmail::FORMAT_HTML)
            ->assign('settings', $this->pluginSettings['settings'])
            ->assign('cart', $this->cart)
            ->assign('orderItem', $orderItem);

        if ($orderItem->getBillingAddress()->getEmail()) {
            $email->replyTo($orderItem->getBillingAddress()->getEmail());
        }
        if ($this->getSellerEmailCc()) {
            $cc = explode(',', $this->getSellerEmailCc());
            $email->cc(...$cc);
        }
        if ($this->getSellerEmailBcc()) {
            $bcc = explode(',', $this->getSellerEmailBcc());
            $email->bcc(...$bcc);
        }

        $this->addAttachments('seller', $orderItem, $email);

        if ($GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface) {
            $email->setRequest($GLOBALS['TYPO3_REQUEST']);
        }

        $this->mailer->send($email);
    }

    public function addAttachments(string $type, Item $orderItem, FluidEmail $email): void
    {
        $attachmentEvent = new AttachmentEvent($type, $orderItem);
        $this->eventDispatcher->dispatch($attachmentEvent);

        $attachments = $attachmentEvent->getAttachments();

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    $email->attachFromPath($attachment);
                }
            }
        }
    }
}
