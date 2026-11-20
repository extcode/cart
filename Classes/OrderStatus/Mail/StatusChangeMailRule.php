<?php

declare(strict_types=1);

namespace Extcode\Cart\OrderStatus\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Mail\AbstractMailConfiguration;
use Extcode\Cart\Mail\BuyerMailConfiguration;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\Mail\SellerMailConfiguration;
use Extcode\Cart\OrderStatus\ServiceType;

/**
 * A rule of plugin.tx_cart.statusChange.mail.<serviceType>.<key>: the emails
 * which are sent for a transition of the status.
 */
final readonly class StatusChangeMailRule
{
    private const RECIPIENTS = ['buyer', 'seller'];

    private const KEY_PATTERN = '/^[a-zA-Z][a-zA-Z0-9]*$/';

    public function __construct(
        public string $key,
        public ServiceType $serviceType,
        /**
         * @var null|list<string> the old status, null for any status ("*")
         */
        public ?array $fromStatus,
        public string $toStatus,
        /**
         * @var list<AbstractMailConfiguration>
         */
        public array $mailConfigurations,
    ) {
    }

    /**
     * Creates the rule from TypoScript as plain array, e.g.
     * ['from' => 'pending', 'to' => 'paid', 'recipients' => ['buyer' => ['fromAddress' => '...']]].
     *
     * Without "template" the template folder is the key with an upper case
     * first letter, e.g. "Mail/StatusChange/Payment/PendingToPaid"; the
     * templates are "Buyer" and "Seller" in this folder.
     *
     * @param array<array-key, mixed> $configuration
     *
     * @throws InvalidMailConfigurationException
     */
    public static function fromArray(string $key, ServiceType $serviceType, array $configuration): self
    {
        $from = is_string($configuration['from'] ?? null) ? trim($configuration['from']) : '';
        $to = is_string($configuration['to'] ?? null) ? trim($configuration['to']) : '';
        if ($from === '') {
            throw new InvalidMailConfigurationException('The old status "from" is missing, use "*" for any status.', 1795120001);
        }
        if ($to === '') {
            throw new InvalidMailConfigurationException('The new status "to" is missing.', 1795120002);
        }

        $template = $configuration['template'] ?? '';
        if (!is_string($template)) {
            throw new InvalidMailConfigurationException('The template must be a string.', 1795120003);
        }
        if ($template === '') {
            if (preg_match(self::KEY_PATTERN, $key) !== 1) {
                throw new InvalidMailConfigurationException('The key is no valid template folder, it must match ' . self::KEY_PATTERN . ', or set "template".', 1795120004);
            }
            $template = 'Mail/StatusChange/' . ucfirst($serviceType->value) . '/' . ucfirst($key);
        }

        $fromStatus = $from === '*'
            ? null
            : array_values(array_filter(array_map(trim(...), explode(',', $from)), static fn (string $status): bool => $status !== ''));

        return new self($key, $serviceType, $fromStatus, $to, self::getMailConfigurations($configuration['recipients'] ?? [], $template));
    }

    public function matches(string $fromStatus, string $toStatus): bool
    {
        return $toStatus === $this->toStatus
            && ($this->fromStatus === null || in_array($fromStatus, $this->fromStatus, true));
    }

    /**
     * @throws InvalidMailConfigurationException
     *
     * @return list<AbstractMailConfiguration>
     */
    private static function getMailConfigurations(mixed $recipients, string $template): array
    {
        if (!is_array($recipients) || $recipients === []) {
            throw new InvalidMailConfigurationException('The "recipients" are missing, e.g. "recipients.buyer".', 1795120005);
        }
        $unknownRecipients = array_diff(array_map(strval(...), array_keys($recipients)), self::RECIPIENTS);
        if ($unknownRecipients !== []) {
            throw new InvalidMailConfigurationException('The recipients "' . implode('", "', $unknownRecipients) . '" are unknown, allowed are "' . implode('", "', self::RECIPIENTS) . '".', 1795120006);
        }

        $mailConfigurations = [];
        foreach (self::RECIPIENTS as $recipient) {
            if (!array_key_exists($recipient, $recipients)) {
                continue;
            }
            $settings = $recipients[$recipient];
            if (!is_array($settings)) {
                throw new InvalidMailConfigurationException('The settings of the recipient "' . $recipient . '" must be an array.', 1795120007);
            }
            $mailConfigurations[] = $recipient === 'buyer'
                ? BuyerMailConfiguration::fromArray($template . '/Buyer', $settings)
                : SellerMailConfiguration::fromArray($template . '/Seller', $settings);
        }

        return $mailConfigurations;
    }
}
