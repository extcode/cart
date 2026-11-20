<?php

declare(strict_types=1);

namespace Extcode\Cart\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\MailConfigurationLoaderInterface;
use Extcode\Cart\Domain\Log\LogServiceInterface;
use Extcode\Cart\Domain\Log\Model\Log;
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\AddressInterface;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Event\Mail\AttachmentEvent;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Sends the emails to the buyer and to the seller at the end of an order with
 * the templates "Mail/<payment status>/Buyer" and "Mail/<payment status>/Seller".
 * The result is written to the order log, a failed email does not stop the order.
 */
final readonly class OrderMailSender
{
    public function __construct(
        private MailConfigurationLoaderInterface $mailConfigurationLoader,
        private Mailer $mailer,
        private ConfigurationManagerInterface $configurationManager,
        private EventDispatcherInterface $eventDispatcher,
        private LogServiceInterface $logService,
    ) {
    }

    /**
     * Sends no email without sender address or without email address of the buyer.
     */
    public function sendBuyerMail(Item $orderItem, ?Cart $cart = null): void
    {
        $billingAddress = $orderItem->getBillingAddress();
        if (!$billingAddress instanceof AddressInterface || trim($billingAddress->getEmail()) === '') {
            return;
        }

        try {
            $mailConfiguration = $this->mailConfigurationLoader->getBuyerMailConfiguration(
                self::getTemplate($orderItem, 'Buyer')
            );
        } catch (InvalidMailConfigurationException $exception) {
            $this->logError($orderItem, 'buyer', $exception);

            return;
        }

        if ($mailConfiguration->fromAddress === '') {
            return;
        }

        $this->send($mailConfiguration, $orderItem, $cart);
    }

    /**
     * Sends no email without sender address. Without email address of the seller an error is logged.
     *
     * @throws InvalidArgumentException if the order is not persisted
     */
    public function sendSellerMail(Item $orderItem, ?Cart $cart = null): void
    {
        self::getOrderItemUid($orderItem);

        try {
            $mailConfiguration = $this->mailConfigurationLoader->getSellerMailConfiguration(
                self::getTemplate($orderItem, 'Seller')
            );
        } catch (InvalidMailConfigurationException $exception) {
            $this->logError($orderItem, 'seller', $exception);

            return;
        }

        if ($mailConfiguration->fromAddress === '') {
            return;
        }

        $this->send($mailConfiguration, $orderItem, $cart);
    }

    /**
     * Sends the email with the attachments of the AttachmentEvent and writes
     * the result to the order log.
     */
    public function send(AbstractMailConfiguration $mailConfiguration, Item $orderItem, ?Cart $cart = null): void
    {
        $recipient = $mailConfiguration->getRecipientName();

        $configuration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
        $request = ($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface ? $GLOBALS['TYPO3_REQUEST'] : null;

        try {
            $this->mailer->send(
                $mailConfiguration,
                $orderItem,
                [
                    'settings' => $configuration['settings'] ?? [],
                    'cart' => $cart,
                ],
                $request,
                $this->getAttachments($recipient, $orderItem),
            );
        } catch (MailException $exception) {
            $this->logError($orderItem, $recipient, $exception);

            return;
        }

        $this->logService->write(
            Log::info(
                self::getOrderItemUid($orderItem),
                self::getLogType($recipient),
                'Mail was send to ' . $recipient . '.',
                [
                    'time' => time(),
                ]
            )
        );
    }

    /**
     * @return list<string> the existing files of the AttachmentEvent, missing files are logged
     */
    private function getAttachments(string $recipient, Item $orderItem): array
    {
        $attachmentEvent = new AttachmentEvent($recipient, $orderItem);
        $this->eventDispatcher->dispatch($attachmentEvent);

        $attachments = [];
        foreach ($attachmentEvent->getAttachments() as $attachment) {
            if (is_file($attachment)) {
                $attachments[] = $attachment;
                continue;
            }
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

        return $attachments;
    }

    private function logError(Item $orderItem, string $recipient, InvalidMailConfigurationException|MailException $exception): void
    {
        $this->logService->write(
            Log::error(
                self::getOrderItemUid($orderItem),
                self::getLogType($recipient),
                'Mail could not send to ' . $recipient . '.',
                [
                    'time' => time(),
                    'exception' => ($exception->getPrevious() ?? $exception)->__toString(),
                ]
            )
        );
    }

    private static function getTemplate(Item $orderItem, string $recipient): string
    {
        return 'Mail/' . ucfirst($orderItem->getPayment()?->getStatus() ?? '') . '/' . $recipient;
    }

    /**
     * @return string e.g. "sendBuyerMail"
     */
    private static function getLogType(string $recipient): string
    {
        return 'send' . ucfirst($recipient) . 'Mail';
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
