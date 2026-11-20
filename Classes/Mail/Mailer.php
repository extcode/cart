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
     * @param list<string> $attachments absolute paths of the files to attach
     *
     * @throws MailException if the email can not be sent, e.g. because the template or an attachment is missing
     */
    public function send(
        AbstractMailConfiguration $mailConfiguration,
        Item $orderItem,
        array $templateVariables = [],
        ?ServerRequestInterface $request = null,
        array $attachments = [],
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
        foreach ($attachments as $attachment) {
            if (!is_file($attachment)) {
                throw new MailException('The attachment "' . $attachment . '" does not exist.', 1795140001);
            }
            $email->attachFromPath($attachment);
        }

        try {
            // renders the template, so errors of the template are reported before sending
            $email->getHtmlBody();
        } catch (InvalidTemplateResourceException $exception) {
            throw new MailException('The template "' . $mailConfiguration->template . '" is missing.', 1795110003, $exception);
        } catch (Throwable $exception) {
            // e.g. a view helper which needs a request, like on the command line
            throw new MailException(
                'The template "' . $mailConfiguration->template . '" could not be rendered: ' . $exception->getMessage(),
                1795140002,
                $exception
            );
        }

        try {
            $this->mailer->send($email);
        } catch (Throwable $exception) {
            throw new MailException('The email could not be sent: ' . $exception->getMessage(), 1795110004, $exception);
        }
    }
}
