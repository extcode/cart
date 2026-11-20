<?php

declare(strict_types=1);

namespace Extcode\Cart\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\AddressInterface;
use Extcode\Cart\Domain\Model\Order\Item;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The configuration of an email to the buyer or to the seller, read from
 * TypoScript with the keys of plugin.tx_cart.mail.buyer or .seller. It is
 * independent of an order; the addresses for an order are the MailEnvelope.
 */
abstract readonly class AbstractMailConfiguration
{
    public function __construct(
        /**
         * The template, e.g. "Mail/StatusChange/Payment/PendingToPaid/Buyer".
         */
        public string $template,
        public string $fromAddress = '',
        public string $fromName = '',
        /**
         * @var list<string>
         */
        public array $cc = [],
        /**
         * @var list<string>
         */
        public array $bcc = [],
    ) {
        self::validateAddresses('fromAddress', $fromAddress === '' ? [] : [$fromAddress]);
        self::validateAddresses('ccAddress', $cc);
        self::validateAddresses('bccAddress', $bcc);
    }

    /**
     * The name of the recipient for messages, e.g. "buyer".
     */
    abstract public function getRecipientName(): string;

    /**
     * @throws MailException if the email can not be sent to this order, e.g. without email address of the buyer
     */
    abstract public function getEnvelope(Item $orderItem): MailEnvelope;

    /**
     * @param list<string> $addresses
     *
     * @throws InvalidMailConfigurationException
     */
    protected static function validateAddresses(string $settingName, array $addresses): void
    {
        foreach ($addresses as $address) {
            if (!GeneralUtility::validEmail($address)) {
                throw new InvalidMailConfigurationException(
                    'The email address "' . $address . '" of "' . $settingName . '" is invalid.',
                    1795130001
                );
            }
        }
    }

    protected static function getBuyerAddress(Item $orderItem): string
    {
        $billingAddress = $orderItem->getBillingAddress();

        return $billingAddress instanceof AddressInterface ? trim($billingAddress->getEmail()) : '';
    }

    /**
     * @param array<array-key, mixed> $settings
     */
    protected static function getString(array $settings, string $key): string
    {
        $value = $settings[$key] ?? '';

        return is_scalar($value) ? trim((string) $value) : '';
    }

    /**
     * @param array<array-key, mixed> $settings
     *
     * @return list<string> the comma separated addresses of the setting
     */
    protected static function getAddresses(array $settings, string $key): array
    {
        return array_values(array_filter(
            array_map(trim(...), explode(',', self::getString($settings, $key))),
            static fn (string $address): bool => $address !== ''
        ));
    }
}
