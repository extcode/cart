<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Status\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\Status\ServiceType;
use Extcode\Cart\Mail\InvalidMailConfigurationException;

/**
 * A rule of the site settings extcode.cart.statusChange.mail.<serviceType>.<key>:
 * the emails which are sent for a transition of the status. The addresses are
 * the ones of extcode.cart.mail.buyer and extcode.cart.mail.seller.
 */
final readonly class StatusChangeMailRule
{
    public const RECIPIENTS = ['buyer', 'seller'];

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
         * The template folder, e.g. "Mail/StatusChange/Payment/PendingToPaid".
         */
        public string $template,
        /**
         * @var list<string> "buyer" and/or "seller"
         */
        public array $recipients,
    ) {
    }

    /**
     * Creates the rule from the site settings, e.g.
     * ['from' => 'pending', 'to' => 'paid', 'recipients' => ['buyer']].
     *
     * "from" is a status, a list of status (also comma separated) or "*" for
     * any status. Without "template" the template folder is the key with an
     * upper case first letter, e.g. "Mail/StatusChange/Payment/PendingToPaid";
     * the templates are "Buyer" and "Seller" in this folder.
     *
     * @param array<array-key, mixed> $configuration
     *
     * @throws InvalidMailConfigurationException
     */
    public static function fromArray(string $key, ServiceType $serviceType, array $configuration): self
    {
        $from = self::getStrings($configuration['from'] ?? null);
        $to = is_string($configuration['to'] ?? null) ? trim($configuration['to']) : '';
        if ($from === []) {
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

        return new self(
            $key,
            $serviceType,
            $from === ['*'] ? null : $from,
            $to,
            $template,
            self::getRecipients($configuration['recipients'] ?? null)
        );
    }

    public function matches(string $fromStatus, string $toStatus): bool
    {
        return $toStatus === $this->toStatus
            && ($this->fromStatus === null || in_array($fromStatus, $this->fromStatus, true));
    }

    /**
     * @param string $recipient "buyer" or "seller"
     *
     * @return string e.g. "Mail/StatusChange/Payment/PendingToPaid/Buyer"
     */
    public function getTemplate(string $recipient): string
    {
        return $this->template . '/' . ucfirst($recipient);
    }

    /**
     * @throws InvalidMailConfigurationException
     *
     * @return list<string>
     */
    private static function getRecipients(mixed $recipients): array
    {
        $recipients = self::getStrings($recipients);
        if ($recipients === []) {
            throw new InvalidMailConfigurationException('The "recipients" are missing, e.g. "recipients: [buyer]".', 1795120005);
        }
        $unknownRecipients = array_diff($recipients, self::RECIPIENTS);
        if ($unknownRecipients !== []) {
            throw new InvalidMailConfigurationException('The recipients "' . implode('", "', $unknownRecipients) . '" are unknown, allowed are "' . implode('", "', self::RECIPIENTS) . '".', 1795120006);
        }

        return array_values(array_intersect(self::RECIPIENTS, $recipients));
    }

    /**
     * @return list<string> the non-empty strings of a list or of a comma separated string
     */
    private static function getStrings(mixed $value): array
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        if (!is_array($value)) {
            return [];
        }

        $strings = [];
        foreach ($value as $string) {
            $string = is_scalar($string) ? trim((string) $string) : '';
            if ($string !== '') {
                $strings[] = $string;
            }
        }

        return $strings;
    }
}
