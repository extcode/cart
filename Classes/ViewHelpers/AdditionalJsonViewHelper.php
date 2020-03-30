<?php

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class AdditionalJsonViewHelper extends AbstractViewHelper
{
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
            'additional',
            'mixed',
            'Array or JSON string',
            true
        );

        $this->registerArgument(
            'key',
            'string',
            'key',
            true
        );
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $content = '';

        $data = $this->arguments['additional'];

        if (is_string($data)) {
            $data = json_decode($data, true);

            if (!$data || empty($data)) {
                return 'Parameter additional seems to be an invalid JSON string!';
            }
        }

        $key = $this->arguments['key'];

        if (!empty($data)) {
            $content = $this->getValue($data, $key);
        }

        return $content;
    }

    /**
     * @param array $data
     * @param string $key
     *
     * @return string
     */
    protected function getValue($data, $key): string
    {
        list($key, $residual) = explode('|', $key, 2);
        if (!empty($residual)) {
            return $this->getValue($data[$key], $residual);
        }

        return $data[$key];
    }
}
