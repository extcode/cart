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
 * An email to the seller, who replies to the buyer.
 */
final readonly class SellerMailConfiguration extends AbstractMailConfiguration
{
    /**
     * @param list<string> $to
     * @param list<string> $cc
     * @param list<string> $bcc
     */
    public function __construct(
        string $template,
        public array $to,
        string $fromAddress = '',
        string $fromName = '',
        array $cc = [],
        array $bcc = [],
    ) {
        parent::__construct($template, $fromAddress, $fromName, $cc, $bcc);
        self::validateAddresses('toAddress', $to);
    }

    /**
     * @param array<array-key, mixed> $settings the keys of plugin.tx_cart.mail.seller: fromName, fromAddress, toAddress, ccAddress, bccAddress
     *
     * @throws InvalidMailConfigurationException without toAddress or with an invalid email address
     */
    public static function fromArray(string $template, array $settings): self
    {
        $to = self::getAddresses($settings, 'toAddress');
        if ($to === []) {
            throw new InvalidMailConfigurationException('The seller has no "toAddress".', 1795110002);
        }

        return new self(
            $template,
            $to,
            self::getString($settings, 'fromAddress'),
            self::getString($settings, 'fromName'),
            self::getAddresses($settings, 'ccAddress'),
            self::getAddresses($settings, 'bccAddress'),
        );
    }

    public function getRecipientName(): string
    {
        return 'seller';
    }

    public function getEnvelope(Item $orderItem): MailEnvelope
    {
        $buyerAddress = self::getBuyerAddress($orderItem);

        return new MailEnvelope($this->fromAddress, $this->fromName, $this->to, $this->cc, $this->bcc, $buyerAddress === '' ? [] : [$buyerAddress]);
    }
}
