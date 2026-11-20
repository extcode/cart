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
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException;
use Throwable;

/**
 * Sends the email of a mail configuration for an order.
 */
final readonly class Mailer
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    /**
     * @param array<string, mixed> $templateVariables additional variables of the template, the order is "orderItem"
     *
     * @throws MailException if the email can not be sent, e.g. because the template is missing
     */
    public function send(
        AbstractMailConfiguration $mailConfiguration,
        Item $orderItem,
        array $templateVariables = [],
        ?ServerRequestInterface $request = null
    ): void {
        $envelope = $mailConfiguration->getEnvelope($orderItem);

        $email = new FluidEmail();
        $email
            ->setTemplate($mailConfiguration->template)
            ->format(FluidEmail::FORMAT_HTML)
            ->assignMultiple($templateVariables)
            ->assign('orderItem', $orderItem)
            ->to(...$envelope->to)
        ;
        if ($request instanceof ServerRequestInterface) {
            $email->setRequest($request);
        }
        if ($envelope->fromAddress !== '') {
            $email->from(new Address($envelope->fromAddress, $envelope->fromName));
        }
        if ($envelope->cc !== []) {
            $email->cc(...$envelope->cc);
        }
        if ($envelope->bcc !== []) {
            $email->bcc(...$envelope->bcc);
        }
        if ($envelope->replyTo !== []) {
            $email->replyTo(...$envelope->replyTo);
        }

        try {
            // renders the template, so a missing template is reported before sending
            $email->getHtmlBody();
        } catch (InvalidTemplateResourceException $exception) {
            throw new MailException('The template "' . $mailConfiguration->template . '" is missing.', 1795110003, $exception);
        }

        try {
            $this->mailer->send($email);
        } catch (Throwable $exception) {
            throw new MailException('The email could not be sent: ' . $exception->getMessage(), 1795110004, $exception);
        }
    }
}
