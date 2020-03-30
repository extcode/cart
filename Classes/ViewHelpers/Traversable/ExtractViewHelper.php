<?php

namespace Extcode\Cart\ViewHelpers\Traversable;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ExtractViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'key',
            'string',
            'Key',
            true
        );
        $this->registerArgument(
            'content',
            \Traversable::class,
            'Content',
            false
        );
        $this->registerArgument(
            'as',
            'string',
            'Which variable to update in the TemplateVariableContainer. If left out, returns the random element instead of updating the variable',
            false
        );
    }

    /**
     * @return array
     */
    public function render()
    {
        $key = $this->arguments['key'];
        $content = $this->arguments['content'];

        if ($content === null) {
            $content = $this->renderChildren();
        }

        try {
            $result = $this->extractByKey($content, $key);
        } catch (\Exception $error) {
            $result = [];
        }

        if (true === isset($this->arguments['as']) && false === empty($this->arguments['as'])) {
            if (true === $this->templateVariableContainer->exists($this->arguments['as'])) {
                $backup = $this->templateVariableContainer->get($this->arguments['as']);
                $this->templateVariableContainer->remove($this->arguments['as']);
            }
            $this->templateVariableContainer->add($this->arguments['as'], $result);
            $content = $this->renderChildren();
            $this->templateVariableContainer->remove($this->arguments['as']);
            if (true === isset($backup)) {
                $this->templateVariableContainer->add($this->arguments['as'], $backup);
            }
            return $content;
        }

        return $result;
    }

    /**
     * Extract by key
     *
     * @param \Traversable $iterator
     * @param string $key
     *
     * @return mixed NULL or whatever we found at $key
     *
     * @throws \Exception
     */
    public function extractByKey($iterator, $key)
    {
        if ((is_array($iterator) === false) && ($iterator instanceof \Traversable === false)) {
            throw new \Exception('Traversable object or array expected but received ' . gettype($iterator), 1361532490);
        }

        $result = $iterator[$key];

        return $result;
    }
}
