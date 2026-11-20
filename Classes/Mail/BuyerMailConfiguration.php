<?php

declare(strict_types=1);

namespace Extcode\Cart\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;

/**
 * An email to the buyer, sent to the email address of the billing address of the order.
 */
final readonly class BuyerMailConfiguration extends AbstractMailConfiguration
{
    /**
     * @param list<string> $cc
     * @param list<string> $bcc
     * @param list<string> $replyTo
     */
    public function __construct(
        string $template,
        string $fromAddress = '',
        string $fromName = '',
        array $cc = [],
        array $bcc = [],
        public array $replyTo = [],
    ) {
        parent::__construct($template, $fromAddress, $fromName, $cc, $bcc);
        self::validateAddresses('replyToAddress', $replyTo);
    }

    /**
     * @param array<array-key, mixed> $settings the keys of plugin.tx_cart.mail.buyer: fromName, fromAddress, ccAddress, bccAddress, replyToAddress
     *
     * @throws InvalidMailConfigurationException with an invalid email address
     */
    public static function fromArray(string $template, array $settings): self
    {
        return new self(
            $template,
            self::getString($settings, 'fromAddress'),
            self::getString($settings, 'fromName'),
            self::getAddresses($settings, 'ccAddress'),
            self::getAddresses($settings, 'bccAddress'),
            self::getAddresses($settings, 'replyToAddress'),
        );
    }

    public function getRecipientName(): string
    {
        return 'buyer';
    }

    public function getEnvelope(Item $orderItem): MailEnvelope
    {
        $buyerAddress = self::getBuyerAddress($orderItem);
        if ($buyerAddress === '') {
            throw new MailException('The order has no email address of the buyer.', 1795110001);
        }

        return new MailEnvelope($this->fromAddress, $this->fromName, [$buyerAddress], $this->cc, $this->bcc, $this->replyTo);
    }
}
