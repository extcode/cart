<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Export;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Extcode\Cart\Event\Order\ExportEvent;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\View\FluidViewAdapter;

/**
 * Renders the export with the Fluid template Backend/Order/Order/Export.<format>.
 *
 * The EventListener is only responsible for a format if such a template exists
 * in one of the template root paths of the backend module, e.g. Export.csv.
 */
final readonly class FluidTemplate
{
    private const CONTROLLER_NAME = 'Backend\Order\Order';

    private const ACTION_NAME = 'Export';

    public function __construct(
        private ConfigurationManagerInterface $configurationManager,
        private ViewFactoryInterface $viewFactory,
        private ItemRepository $itemRepository,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
    ) {}

    public function __invoke(ExportEvent $event): void
    {
        $request = $event->getRequest();
        $format = $event->getFormat();

        $view = $this->viewFactory->create(
            new ViewFactoryData(
                templateRootPaths: $this->getViewPaths('templateRootPaths', 'EXT:cart/Resources/Private/Templates/'),
                partialRootPaths: $this->getViewPaths('partialRootPaths', 'EXT:cart/Resources/Private/Partials/'),
                layoutRootPaths: $this->getViewPaths('layoutRootPaths', 'EXT:cart/Resources/Private/Layouts/'),
                request: $request,
                format: $format,
            )
        );

        if (!$view instanceof FluidViewAdapter) {
            return;
        }

        $renderingContext = $view->getRenderingContext();
        $renderingContext->setControllerName(self::CONTROLLER_NAME);
        $renderingContext->setControllerAction(self::ACTION_NAME);

        $templateFile = $renderingContext->getTemplatePaths()->resolveTemplateFileForControllerAndActionAndFormat(
            self::CONTROLLER_NAME,
            self::ACTION_NAME,
            $format
        );
        if ($templateFile === null) {
            return;
        }

        $view->assignMultiple([
            'settings' => $event->getSettings(),
            'searchArguments' => $event->getSearchArguments(),
            'orderItems' => $this->itemRepository->findAll($event->getSearchArguments()),
            'pdfRendererInstalled' => ExtensionManagementUtility::isLoaded('cart_pdf'),
        ]);

        $filename = 'Order-Export-' . date('Y-m-d_H-i') . '.' . $format;

        $event->setResponse(
            $this->responseFactory->createResponse()
                ->withAddedHeader('Content-Type', 'text/' . $format)
                ->withAddedHeader('Content-Description', 'File transfer')
                ->withAddedHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->withBody($this->streamFactory->createStream($view->render()))
        );
    }

    /**
     * Uses the view configuration of the backend module (module.tx_cart.view) like
     * \TYPO3\CMS\Extbase\Mvc\Controller\ActionController::resolveView() does.
     *
     * @return string[]
     */
    private function getViewPaths(string $type, string $defaultPath): array
    {
        $configuration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        $viewConfiguration = $configuration['view'] ?? [];
        $paths = is_array($viewConfiguration) ? ($viewConfiguration[$type] ?? []) : [];

        if (!is_array($paths) || $paths === []) {
            return [$defaultPath];
        }

        $paths = array_values(array_filter(ArrayUtility::sortArrayWithIntegerKeys($paths), is_string(...)));
        if (!in_array($defaultPath, $paths, true)) {
            $paths = array_merge([$defaultPath], $paths);
        }

        return $paths;
    }
}
