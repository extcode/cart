<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\StatusChange;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Log\LogServiceInterface;
use Extcode\Cart\Domain\Log\Model\Log;
use Extcode\Cart\Event\Order\AfterStatusChangeEvent;
use Extcode\Cart\Mail\MailException;
use Extcode\Cart\Mail\Mailer;
use Extcode\Cart\OrderStatus\Mail\InvalidStatusChangeMailRule;
use Extcode\Cart\OrderStatus\Mail\StatusChangeMailRuleLoader;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\Features;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Sends the emails configured in plugin.tx_cart.statusChange.mail.<serviceType>
 * for the transition of the status. A failed email does not undo the change,
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
        private Mailer $mailer,
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

        $pluginTypoScript = $this->statusChangeMailRuleLoader->loadPluginTypoScript();
        if ($pluginTypoScript === null) {
            $this->logService->write(
                Log::warning(
                    $orderChange->orderItem,
                    self::LOG_TYPE,
                    'No emails of status changes are sent, because the TypoScript is not available, e.g. on the command line.',
                    ['orderChange' => $orderChange->uid]
                )
            );

            return;
        }

        $settings = is_array($pluginTypoScript['settings'] ?? null) ? $pluginTypoScript['settings'] : [];
        $request = ($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface ? $GLOBALS['TYPO3_REQUEST'] : null;

        $templateVariables = ['orderChange' => $orderChange, 'settings' => $settings];

        foreach ($this->statusChangeMailRuleLoader->getRules($pluginTypoScript, $event->getServiceType()) as $rule) {
            if ($rule instanceof InvalidStatusChangeMailRule) {
                $this->reportError($event, $rule->key, '', $rule->reason);
                continue;
            }
            if (!$rule->matches($orderChange->oldValue, $orderChange->newValue)) {
                continue;
            }

            foreach ($rule->mailConfigurations as $mailConfiguration) {
                $recipient = $mailConfiguration->getRecipientName();
                try {
                    $this->mailer->send($mailConfiguration, $event->getOrderItem(), $templateVariables, $request);
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
     * @param string $recipient e.g. "buyer", empty for an invalid rule
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
