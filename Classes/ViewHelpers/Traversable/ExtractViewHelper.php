<?php

namespace Extcode\Cart\ViewHelpers\Traversable;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class ExtractViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Initialize arguments
     *
     * @return void
     */
    public function initializeArguments() {
        $this->registerArgument('as', 'string', 'Which variable to update in the TemplateVariableContainer. If left out, returns the random element instead of updating the variable', FALSE);
    }

    /**
     * @param string $key
     * @param \Traversable $content
     *
     * @return array
     */
    public function render($key, $content = null) {
        if ($content === null) {
            $content = $this->renderChildren();
        }

        try {
            $result = $this->extractByKey($content, $key);
        } catch (\Exception $error) {
            $result = [];
        }

        if (TRUE === isset($this->arguments['as']) && FALSE === empty($this->arguments['as'])) {
            if (TRUE === $this->templateVariableContainer->exists($this->arguments['as'])) {
                $backup = $this->templateVariableContainer->get($this->arguments['as']);
                $this->templateVariableContainer->remove($this->arguments['as']);
            }
            $this->templateVariableContainer->add($this->arguments['as'], $result);
            $content = $this->renderChildren();
            $this->templateVariableContainer->remove($this->arguments['as']);
            if (TRUE === isset($backup)) {
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
    public function extractByKey($iterator, $key) {
        if ((is_array($iterator) === false) && ($iterator instanceof \Traversable === false)) {
            throw new \Exception('Traversable object or array expected but received ' . gettype($iterator), 1361532490);
        }

        $result = $iterator[$key];

        return $result;
    }
}
