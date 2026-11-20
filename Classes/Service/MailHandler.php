<?php

declare(strict_types=1);

namespace Extcode\Cart\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Log\LogServiceInterface;
use Extcode\Cart\Domain\Log\Model\Log;
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\AddressInterface;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Event\Mail\AttachmentEvent;
use Extcode\Cart\EventListener\Order\Finish\Email;
use Extcode\Cart\Mail\BuyerMailConfiguration;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\Mail\OrderMailSender;
use Extcode\Cart\Mail\SellerMailConfiguration;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * @deprecated since cart 12.4, will be removed in cart 13.0. Use \Extcode\Cart\Mail\OrderMailSender to send the
 *             emails of an order, or \Extcode\Cart\Mail\Mailer with a mail configuration of
 *             \Extcode\Cart\Configuration\Loader\MailConfigurationLoaderInterface.
 *             TODO: 13.0.0 Remove the class, see Deprecation #845.
 */
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

    public function __construct(
        private readonly ConfigurationManagerInterface $configurationManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LogServiceInterface $logService,
        private readonly OrderMailSender $orderMailSender,
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
        self::triggerDeprecation();

        if (empty($this->getBuyerEmailFrom())
            || ($orderItem->getBillingAddress() instanceof AddressInterface) === false
            || empty($orderItem->getBillingAddress()->getEmail())
        ) {
            return;
        }

        try {
            $mailConfiguration = new BuyerMailConfiguration(
                'Mail/' . ucfirst($orderItem->getPayment()->getStatus()) . '/Buyer',
                $this->getBuyerEmailFrom(),
                $this->getBuyerEmailName(),
                self::splitAddresses($this->getBuyerEmailCc()),
                self::splitAddresses($this->getBuyerEmailBcc()),
                self::splitAddresses($this->getBuyerEmailReplyTo()),
            );
        } catch (InvalidMailConfigurationException $exception) {
            $this->logInvalidConfiguration($orderItem, 'buyer', $exception);

            return;
        }

        $this->orderMailSender->send($mailConfiguration, $orderItem, $this->cart);
    }

    /**
     * Send a Mail to Seller
     */
    public function sendSellerMail(Item $orderItem): void
    {
        self::triggerDeprecation();

        if (is_null($orderItem->getUid())) {
            throw new InvalidArgumentException('Method should only called for persisted orders.', 1774715307);
        }
        if (empty($this->getSellerEmailFrom()) || empty($this->getSellerEmailTo())) {
            return;
        }

        try {
            $mailConfiguration = new SellerMailConfiguration(
                'Mail/' . ucfirst($orderItem->getPayment()->getStatus()) . '/Seller',
                self::splitAddresses($this->getSellerEmailTo()),
                $this->getSellerEmailFrom(),
                $this->getSellerEmailName(),
                self::splitAddresses($this->getSellerEmailCc()),
                self::splitAddresses($this->getSellerEmailBcc()),
            );
        } catch (InvalidMailConfigurationException $exception) {
            $this->logInvalidConfiguration($orderItem, 'seller', $exception);

            return;
        }

        $this->orderMailSender->send($mailConfiguration, $orderItem, $this->cart);
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
                } else {
                    $this->logService->write(
                        Log::warning(
                            self::getOrderItemUid($orderItem),
                            'addAttachments',
                            'Mail could add attachment ' . $attachment . ' to mail.',
                            [
                                'time' => time(),
                            ]
                        )
                    );
                }
            }
        }
    }

    private static function triggerDeprecation(): void
    {
        trigger_error(
            'Sending the emails of an order with ' . self::class . ' is deprecated since cart 12.4 and will stop working in cart 13.0.'
            . ' Enable the feature toggle "' . Email::FEATURE . '" or use ' . OrderMailSender::class . ' instead.',
            E_USER_DEPRECATED
        );
    }

    /**
     * @return list<string>
     */
    private static function splitAddresses(string $addresses): array
    {
        return array_values(array_filter(
            array_map(trim(...), explode(',', $addresses)),
            static fn (string $address): bool => $address !== ''
        ));
    }

    private function logInvalidConfiguration(Item $orderItem, string $recipient, InvalidMailConfigurationException $exception): void
    {
        $this->logService->write(
            Log::error(
                self::getOrderItemUid($orderItem),
                'send' . ucfirst($recipient) . 'Mail',
                'Mail could not send to ' . $recipient . '.',
                [
                    'time' => time(),
                    'exception' => $exception->__toString(),
                ]
            )
        );
    }

    private static function getOrderItemUid(Item $orderItem): int
    {
        $orderItemUid = $orderItem->getUid();

        if (is_null($orderItemUid)) {
            throw new InvalidArgumentException('Method should only called for persisted orders.', 1774715307);
        }

        return $orderItemUid;
    }
}
