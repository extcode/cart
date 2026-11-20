<?php

declare(strict_types=1);

namespace Extcode\Cart\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * The addresses of one email: sender, recipients, copies and reply-to. All
 * addresses are valid email addresses.
 */
final readonly class MailEnvelope
{
    /**
     * @throws MailException with an invalid email address
     */
    public function __construct(
        public string $fromAddress,
        public string $fromName,
        /**
         * @var list<string>
         */
        public array $to,
        /**
         * @var list<string>
         */
        public array $cc = [],
        /**
         * @var list<string>
         */
        public array $bcc = [],
        /**
         * @var list<string>
         */
        public array $replyTo = [],
    ) {
        $addresses = [...($fromAddress === '' ? [] : [$fromAddress]), ...$to, ...$cc, ...$bcc, ...$replyTo];
        foreach ($addresses as $address) {
            if (!GeneralUtility::validEmail($address)) {
                throw new MailException('The email address "' . $address . '" is invalid.', 1795130002);
            }
        }
    }
}
