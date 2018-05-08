<?php

namespace Extcode\Cart\ViewHelpers;

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper to render the page title
 *
 * # Example: Basic Example
 * # Description: Render the content of the VH as page title
 * <code>
 *    <cart:titleTag>{product.title}</n:titleTag>
 * </code>
 * <output>
 *    <title>TYPO3 is awesome</title>
 * </output>
 */
class TitleTagViewHelper extends AbstractViewHelper implements CompilableInterface
{
    use CompileWithRenderStatic;

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $content = trim($renderChildrenClosure());

        if (!empty($content)) {
            $GLOBALS['TSFE']->altPageTitle = $content;
            $GLOBALS['TSFE']->indexedDocTitle = $content;
        }
    }
}
