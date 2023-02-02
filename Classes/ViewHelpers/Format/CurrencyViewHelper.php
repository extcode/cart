<?php

namespace Extcode\Cart\ViewHelpers\Format;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class CurrencyViewHelper extends AbstractViewHelper
{
    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    public function injectConfigurationManager(ConfigurationManager $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Output is escaped already. We must not escape children, to avoid double encoding.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'currencySign',
            'string',
            'The currency sign. (e.g. $ or €)',
            false
        );
        $this->registerArgument(
            'decimalSeparator',
            'string',
            'The decimal point separator.',
            false
        );
        $this->registerArgument(
            'thousandsSeparator',
            'string',
            'The thousands separator',
            false
        );
        $this->registerArgument(
            'prependCurrency',
            'bool',
            'Select if the curreny sign should be prepended',
            false
        );
        $this->registerArgument(
            'separateCurrency',
            'bool',
            'Separate the currency sign from the number by a single space.',
            false
        );
        $this->registerArgument(
            'decimals',
            'int',
            'Set decimals places.',
            false
        );
        $this->registerArgument(
            'currencyTranslation',
            'float',
            'Set decimals places.',
            false,
            1.00
        );
    }

    /**
     * @return string the formatted amount.
     */
    public function render()
    {
        if ($this->hasArgument('currencySign')) {
            $currencySign = $this->arguments['currencySign'];
        }
        if ($this->hasArgument('decimalSeparator')) {
            $decimalSeparator = $this->arguments['decimalSeparator'];
        }
        if ($this->hasArgument('thousandsSeparator')) {
            $thousandsSeparator = $this->arguments['thousandsSeparator'];
        }
        if ($this->hasArgument('prependCurrency')) {
            $prependCurrency = $this->arguments['prependCurrency'];
        }
        if ($this->hasArgument('separateCurrency')) {
            $separateCurrency = $this->arguments['separateCurrency'];
        }
        if ($this->hasArgument('decimals')) {
            $decimals = $this->arguments['decimals'];
        }
        if ($this->hasArgument('currencyTranslation')) {
            $currencyTranslation = $this->arguments['currencyTranslation'];
        }

        $settings = $this->templateVariableContainer->get('settings');

        if ($settings && $settings['format'] && $settings['format']['currency']) {
            $currencyFormat = $settings['format']['currency'];

            if (!isset($currencySign) && isset($currencyFormat['currencySign'])) {
                $currencySign = $currencyFormat['currencySign'];
            }
            if (!isset($decimalSeparator) && isset($currencyFormat['decimalSeparator'])) {
                $decimalSeparator = $currencyFormat['decimalSeparator'];
            } else {
                $decimalSeparator = '.';
            }
            if (!isset($thousandsSeparator) && isset($currencyFormat['thousandsSeparator'])) {
                $thousandsSeparator = $currencyFormat['thousandsSeparator'];
            } else {
                $thousandsSeparator = ',';
            }
            if (!isset($prependCurrency) && isset($currencyFormat['prependCurrency'])) {
                $prependCurrency = filter_var($currencyFormat['prependCurrency'], FILTER_VALIDATE_BOOLEAN);
            }
            if (!isset($separateCurrency) && isset($currencyFormat['separateCurrency'])) {
                $separateCurrency = filter_var($currencyFormat['separateCurrency'], FILTER_VALIDATE_BOOLEAN);
            }
            if (!isset($decimals) && isset($currencyFormat['decimals'])) {
                $decimals = (int)($currencyFormat['decimals']);
            } else {
                $decimals = 0;
            }
        }

        $thousandsSeparator = $thousandsSeparator ?? ',';
        $decimalSeparator = $decimalSeparator ?? '.';
        $decimals = $decimals ?? 0;

        $floatToFormat = $this->renderChildren();
        if (empty($floatToFormat)) {
            $floatToFormat = 0.0;
        } else {
            $floatToFormat = (float)$floatToFormat;
        }

        if (isset($currencyTranslation) && $currencyTranslation > 0.0) {
            $floatToFormat = $floatToFormat / $currencyTranslation;
        }

        $output = number_format($floatToFormat, $decimals, $decimalSeparator, $thousandsSeparator);
        if (isset($currencySign) && $currencySign !== '') {
            $currencySeparator = isset($separateCurrency) ? ' ' : '';
            if (isset($prependCurrency) && $prependCurrency === true) {
                $output = $currencySign . $currencySeparator . $output;
            } else {
                $output = $output . $currencySeparator . $currencySign;
            }
        }
        return $output;
    }
}
