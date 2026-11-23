<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\StatusChange;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\MailConfigurationLoaderInterface;
use Extcode\Cart\Domain\Log\LogServiceInterface;
use Extcode\Cart\Domain\Log\Model\Log;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Order\Status\Mail\InvalidStatusChangeMailRule;
use Extcode\Cart\Domain\Order\Status\Mail\StatusChangeMailRule;
use Extcode\Cart\Domain\Order\Status\Mail\StatusChangeMailRuleLoader;
use Extcode\Cart\Event\Order\AfterStatusChangeEvent;
use Extcode\Cart\Mail\AbstractMailConfiguration;
use Extcode\Cart\Mail\AttachmentCollector;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\Mail\MailException;
use Extcode\Cart\Mail\Mailer;
use Extcode\Cart\Mail\PluginConfigurationProvider;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\Features;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Sends the emails configured in the site settings
 * extcode.cart.statusChange.mail.<serviceType> of the site of the order for the
 * transition of the status, with the addresses of extcode.cart.mail and the
 * attachments of the AttachmentEvent. A failed email does not undo the change,
 * it is added as error to the event and written to the order log.
 *
 * Experimental: only active with the feature toggle
 * $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['cart.experimental.statusChangeMails'],
 * classes and configuration may change in minor versions.
 */
final readonly class Mail
{
    public const FEATURE = 'cart.experimental.statusChangeMails';

    private const LOG_TYPE = 'statusChangeMail';

    public function __construct(
        private StatusChangeMailRuleLoader $statusChangeMailRuleLoader,
        private MailConfigurationLoaderInterface $mailConfigurationLoader,
        private Mailer $mailer,
        private AttachmentCollector $attachmentCollector,
        private PluginConfigurationProvider $pluginConfigurationProvider,
        private LogServiceInterface $logService,
        private Features $features,
    ) {
    }

    public function __invoke(AfterStatusChangeEvent $event): void
    {
        if (!$this->features->isFeatureEnabled(self::FEATURE)) {
            return;
        }

        $orderChange = $event->getOrderChange();
        $orderItem = $event->getOrderItem();

        $rules = $this->statusChangeMailRuleLoader->getRules($orderItem, $event->getServiceType());
        if ($rules === null) {
            $this->logService->write(
                Log::warning(
                    $orderChange->orderItem,
                    self::LOG_TYPE,
                    'No emails of status changes are sent, because the storage page of the order is in no site.',
                    ['orderChange' => $orderChange->uid]
                )
            );

            return;
        }

        $request = ($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface ? $GLOBALS['TYPO3_REQUEST'] : null;
        $templateVariables = ['orderChange' => $orderChange, 'settings' => $this->pluginConfigurationProvider->getSettings()];

        foreach ($rules as $rule) {
            if ($rule instanceof InvalidStatusChangeMailRule) {
                $this->reportError($event, $rule->key, '', $rule->reason);
                continue;
            }
            if (!$rule->matches($orderChange->oldValue, $orderChange->newValue)) {
                continue;
            }

            foreach ($rule->recipients as $recipient) {
                try {
                    $mailConfiguration = $this->getMailConfiguration($rule, $recipient, $orderItem);
                } catch (InvalidMailConfigurationException $exception) {
                    $this->reportError($event, $rule->key, '', $exception->getMessage());
                    continue;
                }

                try {
                    $this->mailer->send(
                        $mailConfiguration,
                        $orderItem,
                        $templateVariables,
                        $request,
                        $this->attachmentCollector->collect($recipient, $orderItem)
                    );
                    $this->logService->write(
                        Log::info(
                            $orderChange->orderItem,
                            self::LOG_TYPE,
                            'The email "' . $rule->key . '" was sent to the ' . $recipient . '.',
                            ['orderChange' => $orderChange->uid]
                        )
                    );
                } catch (MailException $exception) {
                    $this->reportError($event, $rule->key, $recipient, $exception->getMessage(), $exception);
                }
            }
        }
    }

    /**
     * @param string $recipient "buyer" or "seller"
     *
     * @throws InvalidMailConfigurationException
     */
    private function getMailConfiguration(StatusChangeMailRule $rule, string $recipient, Item $orderItem): AbstractMailConfiguration
    {
        $template = $rule->getTemplate($recipient);

        return $recipient === 'buyer'
            ? $this->mailConfigurationLoader->getBuyerMailConfiguration($template, $orderItem)
            : $this->mailConfigurationLoader->getSellerMailConfiguration($template, $orderItem);
    }

    /**
     * @param string $recipient e.g. "buyer", empty for an invalid configuration
     */
    private function reportError(AfterStatusChangeEvent $event, string $key, string $recipient, string $reason, ?MailException $exception = null): void
    {
        $orderChange = $event->getOrderChange();
        $message = $recipient === ''
            ? self::translate('invalid_configuration', [$key, $reason])
            : self::translate('not_sent', [$key, $recipient, $reason]);

        $event->addError($message);
        $this->logService->write(
            Log::error(
                $orderChange->orderItem,
                self::LOG_TYPE,
                $message,
                array_filter([
                    'orderChange' => $orderChange->uid,
                    'exception' => $exception?->getPrevious()?->__toString(),
                ])
            )
        );
    }

    /**
     * @param list<string> $arguments
     */
    private static function translate(string $key, array $arguments): string
    {
        return LocalizationUtility::translate('tx_cart.status_change.mail.' . $key, 'Cart', $arguments)
            ?? $key . ': ' . implode(', ', $arguments);
    }
}
